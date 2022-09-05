<?php

namespace App\Models\Product;

use Carbon\Carbon;
use App\Models\BaseModel;
use App\Models\Category\Category;
use App\Models\ProductField\ProductField;
use App\Models\ProductField\FieldTypeName;
use App\Services\Elasticsearch\Searchable;
use App\Models\ProductDetail\ProductDetail;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Elasticsearch\SearchableTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * PROPERTIES
 * @property int id
 * @property string sku
 * @property string name
 * @property int category_id
 * @property int price
 * @property int quantity
 * @property Carbon created_at
 * @property Carbon updated_at
 * RELATIONS
 * @property Category category
 * @property Collection<int, ProductField>|null fields
 * @property Collection<int, ProductDetail>|null details
 */
class Product extends BaseModel implements Searchable
{
    use HasFactory;
    use SearchableTrait;
    use HasRelationships;

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'price',
        'quantity',
    ];

    public static array $allowedIncludes = [
        'category',
        'details',
        'details.field',
    ];

    public static array $requiredRelationsMatch = [
        'details' => [
            'details.field.type'
        ],
        'details.field' => [
            'details.field.type'
        ],
        'short-details' => [
            'details.field.type',
            'details.field.group'
        ]
    ];

    public const ELASTIC_INDEX = 'products';

    public function store(array $data): void
    {
        if (isset($data['details'])) {
            $productFields = ProductField::query()
                ->whereIn('id', array_keys($data['details']))
                ->with('type')
                ->get();

            foreach ($data['details'] as $key => $value) {
                /** @var ProductField $field */
                $field = $productFields->where('id', $key)->first();

                if (!$field) {
                    throw new HttpException(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        sprintf(
                            'Product field with id %s not found.',
                            $key
                        )
                    );
                }

                if (! $field->type->acceptDataType($value)) {
                    throw new HttpException(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        sprintf(
                            'Value for field %s in group %s must be of type %s.',
                            $field->name,
                            $field->group->name,
                            $field->type->name
                        )
                    );
                }

                if ($field->hasType(FieldTypeName::date)) {
                    $data['details'][$key] = Carbon::parse($value);
                }
            }
        }

        $this->fill($data)->save();

        if (isset($data['details'])) {
            $existingDetails = $this->details()->get();

            /** @var ProductDetail $detail */
            foreach ($existingDetails as $detail) {
                $detail->delete();
            }

            foreach ($data['details'] as $key => $value) {
                $this->details()->create([
                    'product_field_id' => $key,
                    'value' => $value
                ]);
            }

            $this->fireModelEvent('updated');
        }
    }

    public function toSearchArray(): array
    {
        $this->append('short_details');

        return $this->toArray();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function fields(): HasManyThrough
    {
        return $this->hasManyDeep(
            ProductField::class,
            [ProductDetail::class],
            [
                'product_id',
                'id'
            ],
            [
                'id',
                'product_field_id'
            ]
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            ProductDetail::class,
            'product_id',
            'id'
        );
    }

    protected function getShortDetailsAttribute(): array
    {
        /** @var Collection<int, ProductDetail>|null $productDetails */
        $productDetails = $this->details()->with('field.type', 'field.group')->get();

        $result = [];

        /** @var ProductDetail $detail */
        foreach ($productDetails as $detail) {
            $result[$detail->field->group->name][$detail->field->name] = $detail->value;
        }

        return $result;
    }
}

<?php

namespace App\Models\Product;

use Carbon\Carbon;
use App\Models\BaseModel;
use App\Models\Category\Category;
use App\Models\FieldType\FieldTypeName;
use App\Models\ProductField\ProductField;
use App\Services\Elasticsearch\Searchable;
use App\Models\ProductDetail\ProductDetail;
use App\Exceptions\InvalidDataTypeException;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Elasticsearch\SearchableTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Services\Elasticsearch\Repositories\Product\ProductSearchRepository;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $sku
 * @property string $name
 * @property int $category_id
 * @property int $price
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * RELATIONS
 * @property Category $category
 * @property Collection|iterable<int, ProductField>|null $fields
 * @property Collection|iterable<int, ProductDetail>|null $details
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
        'details' => ['details.field.type'],
        'details.field' => ['details.field.type'],
        'short-details' => ['details.field.type', 'details.field.group'],
    ];

    /***********************************************************************
     *                                                                     *
     *                              RELATIONS                              *
     *                                                                     *
     **********************************************************************/

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            Category::class,
            'category_id',
            'id'
        );
    }

    public function fields(): HasManyThrough
    {
        return $this->hasManyDeep(
            ProductField::class,
            [ProductDetail::class],
            [
                'product_id',
                'id',
            ],
            [
                'id',
                'product_field_id',
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

    /***********************************************************************
     *                                                                     *
     *                               SCOPES                                *
     *                                                                     *
     **********************************************************************/

    /***********************************************************************
     *                                                                     *
     *                               SETTERS                               *
     *                                                                     *
     **********************************************************************/

    /***********************************************************************
     *                                                                     *
     *                               GETTERS                               *
     *                                                                     *
     **********************************************************************/

    protected function getShortDetailsAttribute(): array
    {
        /** @var Collection|iterable<int, ProductDetail>|null $productDetails */
        $productDetails = $this->details()->with('field.type', 'field.group')->get();

        $result = [];

        /** @var ProductDetail $detail */
        foreach ($productDetails as $detail) {
            $result[$detail->field->group->name][$detail->field->name] = $detail->value;
        }

        return $result;
    }

    /***********************************************************************
     *                                                                     *
     *                              FUNCTIONS                              *
     *                                                                     *
     **********************************************************************/

    /**
     * @throws InvalidDataTypeException
     */
    public function store(array $data): void
    {
        if (isset($data['details'])) {
            $productFields = ProductField::query()
                ->whereIn('id', array_column($data['details'], 'product_field_id'))
                ->with('type')
                ->get();

            foreach ($data['details'] as $key => $detail) {
                /** @var ProductField $field */
                $field = $productFields
                    ->where('id', $detail['product_field_id'])
                    ->first();

                try {
                    $field->type->validateDataType($detail['value']);
                } catch (InvalidDataTypeException $exception) {
                    throw new InvalidDataTypeException(
                        sprintf(
                            'Invalid data type for \'%s\' field. %s',
                            $field->name,
                            $exception->getMessage()
                        )
                    );
                }

                if ($field->hasType(FieldTypeName::Date)) {
                    $data['details'][$key]['value'] = Carbon::parse($data['details'][$key]['value']);
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

            foreach ($data['details'] as $detail) {
                $this->details()->create([
                    'product_field_id' => $detail['product_field_id'],
                    'value' => $detail['value'],
                ]);
            }

            $this->fireModelEvent('saved');
        }
    }

    public function searchRepositoryClass(): string
    {
        return ProductSearchRepository::class;
    }
}

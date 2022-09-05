<?php

namespace App\Models\ProductField;

use Carbon\Carbon;
use RuntimeException;
use App\Models\BaseModel;
use App\Models\Product\Product;
use Ramsey\Collection\Collection;
use App\Jobs\UpdateProductsMapping;
use App\Models\ProductDetail\ProductDetail;
use Symfony\Component\HttpFoundation\Response;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * PROPERTIES
 * @property int id
 * @property string name
 * @property int field_type_id
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property int product_field_group_id
 * RELATIONS
 * @property FieldType type
 * @property ProductFieldGroup group
 * @property Collection<int, Product>|null products
 */
class ProductField extends BaseModel
{
    use HasFactory;
    use HasRelationships;

    protected $fillable = [
        'name',
        'product_field_group_id',
        'field_type_id',
    ];

    public static array $allowedIncludes = [
        'type',
        'group'
    ];

    public function store(array $data): void
    {
        $this->product_field_group_id = $data['product_field_group_id'];
        $this->name = $data['name'];
        $this->field_type_id = $data['field_type_id'];

        $this->save();

        dispatch(new UpdateProductsMapping($this));

        $this->unsetRelations();
    }

    public function setProductFieldGroupIdAttribute(int $value): void
    {
        if ($this->product_field_group_id) {
            throw new RuntimeException(
                'Product field group id updating is restricted.',
                Response::HTTP_CONFLICT
            );
        }

        $this->attributes['product_field_group_id'] = $value;
    }

    public function setNameAttribute(string $value): void
    {
        if ($this->name) {
            throw new RuntimeException(
                'Name updating is restricted.',
                Response::HTTP_CONFLICT
            );
        }

        if (! $this->product_field_group_id) {
            throw new RuntimeException(
                'Object\'s relation Product field group is not set.'
            );
        }

        $exists = ProductField::query()
            ->where('name', $value)
            ->first();

        if ($exists) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'The name has already been taken.'
            );
        }

        $this->attributes['name'] = $value;
    }

    public function setFieldTypeIdAttribute(int $value): void
    {
        if ($this->field_type_id) {
            throw new RuntimeException(
                'Field type id updating is restricted.',
                Response::HTTP_CONFLICT
            );
        }

        $this->attributes['field_type_id'] = $value;
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            ProductDetail::class,
            'product_field_id',
            'id',
            'id',
            'product_id'
        );
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(
            FieldType::class,
            'field_type_id',
            'id'
        );
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(
            ProductFieldGroup::class,
            'product_field_group_id',
            'id'
        );
    }

    public function hasProducts(): bool
    {
        return $this->products()->count();
    }

    public function getField(): string
    {
        return $this->group->name === 'Default'
            ? $this->name
            : 'short_details.' . $this->group->name . '.' . $this->name;
    }

    public function getType(): FieldType
    {
        return $this->type;
    }

    public function getIndex(): string
    {
        return Product::ELASTIC_INDEX;
    }

    public function hasType(FieldTypeName ...$fieldTypeNames): bool
    {
        $typeNames = [];

        foreach ($fieldTypeNames as $typeName) {
            $typeNames[] = $typeName->value();
        }

        return in_array($this->type->name, $typeNames);
    }
}

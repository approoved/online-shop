<?php

namespace App\Models\ProductField;

use Carbon\Carbon;
use RuntimeException;
use App\Models\BaseModel;
use App\Models\Product\Product;
use App\Jobs\UpdateProductsMapping;
use App\Models\FieldType\FieldType;
use App\Models\FieldType\FieldTypeName;
use App\Models\ProductDetail\ProductDetail;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\InvalidInputDataException;
use App\Models\ProductFieldGroup\ProductFieldGroup;
use App\Models\ProductFilterType\ProductFilterType;
use App\Exceptions\InvalidAppConfigurationException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $name
 * @property int $field_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $product_field_group_id
 * RELATIONS
 * @property FieldType $type
 * @property ProductFieldGroup $group
 * @property Collection|iterable<int, Product>|null $products
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
        'group',
    ];

    /***********************************************************************
     *                                                                     *
     *                              RELATIONS                              *
     *                                                                     *
     **********************************************************************/

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

    public function setProductFieldGroupIdAttribute(int $value): void
    {
        if ($this->product_field_group_id) {
            throw new RuntimeException(
                'Product field group id updating is restricted.',
            );
        }

        $this->attributes['product_field_group_id'] = $value;
    }

    /**
     * @throws InvalidInputDataException
     */
    public function setNameAttribute(string $value): void
    {
        if ($this->name) {
            throw new RuntimeException(
                'Product field name updating is restricted.',
            );
        }

        if (! $this->product_field_group_id) {
            throw new RuntimeException(
                'Object\'s relation \'product field group\' is not set.'
            );
        }

        $exists = ProductField::query()
            ->where('name', $value)
            ->first();

        if ($exists) {
            throw new InvalidInputDataException(
                'The name has already been taken. Delete existing field first.'
            );
        }

        $this->attributes['name'] = $value;
    }

    public function setFieldTypeIdAttribute(int $value): void
    {
        if ($this->field_type_id) {
            throw new RuntimeException(
                'Field type id updating is restricted.',
            );
        }

        $this->attributes['field_type_id'] = $value;
    }

    /***********************************************************************
     *                                                                     *
     *                               GETTERS                               *
     *                                                                     *
     **********************************************************************/

    /***********************************************************************
     *                                                                     *
     *                              FUNCTIONS                              *
     *                                                                     *
     **********************************************************************/

    public function store(array $data): void
    {
        $this->product_field_group_id = $data['product_field_group_id'];
        $this->name = $data['name'];
        $this->field_type_id = $data['field_type_id'];

        $this->save();

        dispatch(new UpdateProductsMapping($this));

        $this->unsetRelations();
    }

    public function hasProducts(): bool
    {
        return $this->products()->count();
    }

    public function getField(): string
    {
        return sprintf('short_details.%s.%s', $this->group->name, $this->name);
    }

    public function hasType(FieldTypeName ...$fieldTypeNames): bool
    {
        $typeNames = [];

        foreach ($fieldTypeNames as $typeName) {
            $typeNames[] = $typeName->value();
        }

        return in_array($this->type->name, $typeNames);
    }

    /**
     * @throws InvalidAppConfigurationException
     */
    public function getAvailableFilterTypes(): Collection
    {
        $config = config('field-filter-types-match');

        if (! isset($config[$this->type->name])) {
            throw new InvalidAppConfigurationException(
                sprintf(
                    'Filter types match not configurated for \'%s\' field type.',
                    $this->type->name
                )
            );
        }

        $filterTypeNames = $config[$this->type->name];

        foreach ($filterTypeNames as $key => $filterTypeName) {
            $filterTypeNames[$key] = $filterTypeName->value();
        }

        return ProductFilterType::getSearchQuery()
            ->whereIn('name', $filterTypeNames)
            ->get();
    }
}

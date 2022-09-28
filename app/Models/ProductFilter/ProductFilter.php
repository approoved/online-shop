<?php

namespace App\Models\ProductFilter;

use Carbon\Carbon;
use App\Models\BaseModel;
use App\Models\Product\Product;
use App\Models\Category\Category;
use App\Models\ProductField\ProductField;
use App\Models\ProductDetail\ProductDetail;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\InvalidInputDataException;
use App\Models\ProductFilterType\ProductFilterType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Exceptions\InvalidAppConfigurationException;
use App\Models\ProductFilterValue\ProductFilterValue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ProductFilterType\ProductFilterTypeName;
use App\Models\ProductFilter\Exceptions\InvalidFilterTypeException;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $name
 * @property int $product_filter_type_id
 * @property int $product_field_id
 * @property int $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection&iterable<int, ProductDetail>|null $details
 * RELATIONS
 * @property Category $category
 * @property ProductFilterType $type
 * @property Collection&iterable<int, ProductFilterValue>|null $values
 * @property ProductField $field
 */
final class ProductFilter extends BaseModel
{
    use HasFactory;
    use HasRelationships;

    public static array $allowedIncludes = [
        'values',
        'field',
        'field.type',
        'type',
        'category',
    ];

    public static array $requiredRelationsMatch = [
        'aggregated-values' => ['type', 'values', 'category.products.details'],
    ];

    protected $fillable = [
        'category_id',
        'name',
        'product_field_id',
        'product_filter_type_id',
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

    public function type(): BelongsTo
    {
        return $this->belongsTo(
            ProductFilterType::class,
            'product_filter_type_id',
            'id'
        );
    }

    public function values(): HasMany
    {
        return $this->hasMany(
            ProductFilterValue::class,
            'product_filter_id',
            'id'
        );
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(
            ProductField::class,
            'product_field_id',
            'id'
        );
    }

    /***********************************************************************
     *                                                                     *
     *                               GETTERS                               *
     *                                                                     *
     **********************************************************************/

    public function getDetailsAttribute(): array
    {
        $details = [];

        /** @var Product $product */
        foreach ($this->category->products as $product) {
            /** @var ProductDetail $detail */
            foreach ($product->details as $detail) {
                if ($detail->product_field_id === $this->product_field_id) {
                    $details[] = $detail;
                }
            }
        }

        return $details;
    }

    /***********************************************************************
     *                                                                     *
     *                              FUNCTIONS                              *
     *                                                                     *
     **********************************************************************/

    /**
     * @throws InvalidFilterTypeException
     * @throws InvalidAppConfigurationException
     * @throws InvalidInputDataException
     */
    public function store(array $data): void
    {
        /** @var Category $category */
        $category = Category::query()
            ->find($data['category_id'] ?? $this->category_id);

        if ($category->hasDescendants()) {
            throw new InvalidInputDataException(
                'Unable to create filter for parent category. Delete child categories first.'
            );
        }

        if (isset($data['product_field_id']) || isset($data['product_filter_type_id'])) {
            /** @var ProductFilterType $productFilterType */
            $productFilterType = ProductFilterType::query()
                ->find($data['product_filter_type_id'] ?? $this->product_filter_type_id);

            /** @var ProductField $productField */
            $productField = ProductField::query()
                ->find($data['product_field_id'] ?? $this->product_field_id);

            $availableTypes = $productField->getAvailableFilterTypes();

            if (! $availableTypes->contains('id', $productFilterType->id)) {
                throw new InvalidFilterTypeException(
                    sprintf(
                        'Unable to create %s type filter for %s type field.',
                        $productFilterType->name,
                        $productField->type->name
                    )
                );
            }

            $availableFields = $category->fields()->get();

            if (! $availableFields->contains('id', $productField->id)) {
                throw new InvalidInputDataException(
                    sprintf(
                        'Field %s does not exist in category %s. Add products with this field first.',
                        $productField->name,
                        $category->name
                    )
                );
            }

            /** @var ProductFilter|null $exists */
            $exists = $category
                ->filters()
                ->where('product_field_id', $productField->id)
                ->first();

            if ($exists && $this->id !== $exists->id) {
                throw new InvalidInputDataException(
                    sprintf(
                        'Filter with field \'%s\' already exists in category \'%s\'. Delete existing filter first.',
                        $productField->name,
                        $category->name,
                    )
                );
            }
        }

        $this->fill($data)->save();
    }

    public function hasType(ProductFilterTypeName ...$types): bool
    {
        $typeNames = [];

        foreach ($types as $type) {
            $typeNames[] = $type->value();
        }

        return in_array($this->type->name, $typeNames);
    }
}

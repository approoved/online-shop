<?php

namespace App\Models\ProductFilterValue;

use Carbon\Carbon;
use RuntimeException;
use App\Models\BaseModel;
use App\Models\FieldType\FieldTypeName;
use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidInputDataException;
use App\Exceptions\InvalidAppConfigurationException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ProductFilterType\ProductFilterTypeName;
use App\Models\ProductFilterValue\Serializers\SearchValueSerializer;
use App\Models\ProductFilterValue\Exceptions\InvalidFilterTypeException;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $value
 * @property array $search_value
 * @property int $product_filter_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * RELATIONS
 * @property ProductFilter $filter
 */
class ProductFilterValue extends BaseModel
{
    use HasFactory;

    protected $fillable = ['product_filter_id', 'value', 'search_value'];

    protected $with = ['filter.field', 'filter.type'];

    /***********************************************************************
     *                                                                     *
     *                              RELATIONS                              *
     *                                                                     *
     **********************************************************************/

    public function filter(): BelongsTo
    {
        return $this->belongsTo(
            ProductFilter::class,
            'product_filter_id',
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

    /**
     * @throws InvalidFilterTypeException
     */
    public function setProductFilterIdAttribute($value): void
    {
        /** @var ProductFilter $filter */
        $filter = ProductFilter::query()->find($value);

        if ($filter->hasType(ProductFilterTypeName::Runtime)) {
            throw new InvalidFilterTypeException(
                'Unable to create value for Runtime filter.'
            );
        }

        $this->attributes['product_filter_id'] = $filter->id;
    }

    /**
     * @throws InvalidInputDataException
     */
    public function setValueAttribute($value): void
    {
        if (! $this->filter) {
            throw new RuntimeException(
                'Object\'s relation filter is not set.'
            );
        }

        /** @var ProductFilterValue $exists */
        $exists = $this
            ->filter
            ->values()
            ->where('value', '=', $value)
            ->first();

        if ($exists && $this->id !== $exists->id) {
            throw new InvalidInputDataException(
                sprintf(
                    'Value \'%s\' already exists for \'%s\' filter.',
                    $value,
                    $this->filter->name
                )
            );
        }

        $this->attributes['value'] = $value;
    }

    /**
     * @throws InvalidAppConfigurationException
     */
    public function setSearchValueAttribute($value): void
    {
        if (! $this->filter) {
            throw new RuntimeException(
                'Object\'s relation filter is not set.'
            );
        }

        $value = SearchValueSerializer::serialize($this->filter, $value);

        $this->attributes['search_value'] = json_encode($value);
    }

    /***********************************************************************
     *                                                                     *
     *                               GETTERS                               *
     *                                                                     *
     **********************************************************************/

    public function getSearchValueAttribute($value): array
    {
        $value = json_decode($value, true);

        if ($this->filter->field->hasType(FieldTypeName::Date)) {
            if ($this->filter->hasType(ProductFilterTypeName::Range)) {
                if (isset($value['from'])) {
                    $value['from'] = Carbon::parse($value['from']);
                }

                if (isset($value['to'])) {
                    $value['to'] = Carbon::parse($value['to']);
                }
            }

            if ($this->filter->hasType(ProductFilterTypeName::Exact)) {
                foreach ($value['terms'] as $key => $term) {
                    $value['terms'][$key] = Carbon::parse($term);
                }
            }
        }

        return $value;
    }

    /***********************************************************************
     *                                                                     *
     *                              FUNCTIONS                              *
     *                                                                     *
     **********************************************************************/

    public function store(array $data): void
    {
        if (isset($data['product_filter_id'])) {
            $this->product_filter_id = $data['product_filter_id'];
        }

        $this->fill($data)->save();
        $this->unsetRelations();
    }
}

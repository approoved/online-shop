<?php

namespace App\Models\ProductFilter;

use Carbon\Carbon;
use RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * PROPERTIES
 * @property int id
 * @property int product_filter_id
 * @property string value
 * @property array search_value
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * RELATIONS
 * @property ProductFilter filter
 */
final class ProductFilterValue extends Model
{
    use HasFactory;

    protected $casts = [
        'search_value' => 'array',
    ];

    protected $fillable = [
        'product_filter_id',
        'value',
        'search_value',
    ];

    private function getSearchValueStructure(): array
    {
        if (! $this->product_filter_id) {
            throw new RuntimeException(
                'Object\'s relation filter is not set.'
            );
        }

        return match ($this->filter->type->name) {
            ProductFilterTypeName::Range->value() => ['from' => '', 'to' => ''],
            ProductFilterTypeName::Exact->value() => ['terms' => ''],
            'default' => throw new RuntimeException(
                'Search value structure not set for '
                . $this->filter->type->name . ' filter type.'
            ),
        };
    }

    public function store(array $data): void
    {
        if (isset($data['product_filter_id'])) {
            $this->product_filter_id = $data['product_filter_id'];
        }

        $this->fill($data)->save();
        $this->unsetRelations();
    }

    public function setProductFilterIdAttribute($value): void
    {
        /** @var ProductFilter $filter */
        $filter = ProductFilter::query()->find($value);

        if (! $filter) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND,
                'Product filter not found.'
            );
        }

        if ($filter->hasType(ProductFilterTypeName::Runtime)) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Unable to create value for Runtime filter.'
            );
        }

        $this->attributes['product_filter_id'] = $filter->id;
    }

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
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Value "' . $value . '" already exists for ' . $this->filter->name . ' filter.'
            );
        }

        $this->attributes['value'] = $value;
    }

    protected function setSearchValueAttribute($value): void
    {
        if (! $this->filter) {
            throw new RuntimeException(
                'Object\'s relation filter is not set.'
            );
        }

        $missedKeys = [];
        $searchValueStructure = $this->getSearchValueStructure();

        foreach ($searchValueStructure as $key => $val) {
            if (! array_key_exists($key, $value)) {
                $missedKeys[] = $key;
            }
        }

        if (count($searchValueStructure) === count($missedKeys)) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'Key ' . implode(' or ', $missedKeys) . ' is required for ' .
                $this->filter->type->name . ' filter type.'
            );
        }

        $this->attributes['search_value'] = json_encode(array_intersect_key(
            $value,
            $searchValueStructure
        ));

        $this->unsetRelation('filter');
    }

    public function filter(): BelongsTo
    {
        return $this->belongsTo(ProductFilter::class, 'product_filter_id', 'id');
    }
}

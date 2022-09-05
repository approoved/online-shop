<?php

namespace App\Models\ProductFilter;

use Carbon\Carbon;
use App\Models\Category\Category;
use Ramsey\Collection\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Services\Elasticsearch\Elasticsearch;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

/**
 * PROPERTIES
 * @property int id
 * @property string name
 * @property string field
 * @property int product_filter_type_id
 * @property int category_id
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * RELATIONS
 * @property Category category
 * @property ProductFilterType type
 * @property Collection<int, ProductFilterValue>|null|array values
 */
final class ProductFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'field',
        'product_filter_type_id',
    ];

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function store(array $data): void
    {
        if (isset($data['category_id'])) {
            /** @var Category $category */
            $category = Category::query()->find($data['category_id']);

            if (! $category) {
                throw new HttpException(
                    Response::HTTP_NOT_FOUND,
                    'Category not found.'
                );
            }

            if ($category->hasDescendants()) {
                throw new HttpException(
                    Response::HTTP_CONFLICT,
                    'Unable to create filter for parent category.'
                );
            }
        } else {
            $category = $this->category;
        }

        if (isset($data['field']) || isset($data['product_filter_type_id'])) {
            /** @var ProductFilterType $productFilterType */
            $productFilterType = ProductFilterType::query()->find(
                $data['product_filter_type_id'] ?? $this->product_filter_type_id
            );

            if (! $productFilterType) {
                throw new HttpException(
                    Response::HTTP_NOT_FOUND,
                    'Filter type not found.'
                );
            }

            $availableTypes = Elasticsearch::getInstance()
                ->getFieldFilterTypeList($category, $data['field'] ?? $this->field);

            if (! $availableTypes->contains('id', $productFilterType->id)) {
                throw new HttpException(
                    Response::HTTP_CONFLICT,
                    'Unavailable filter type for this field.'
                );
            }

            if (isset($data['field'])) {
                if (! in_array($data['field'], Elasticsearch::getInstance()->getFields('products', ['category_id' => $category->id]))) {
                    throw new HttpException(
                        Response::HTTP_CONFLICT,
                        'Field ' . $data['field'] .  ' does not exist in category ' . $category->name
                    );
                }

                /** @var ProductFilter|null $exists */
                $exists = $category
                    ->filters()
                    ->where('field', $data['field'])
                    ->first();

                if ($exists && $this->id !== $exists->id) {
                    throw new HttpException(
                        Response::HTTP_CONFLICT,
                        sprintf(
                            'Filter with field %s already exists in category %s',
                            $data['field'],
                            $category->name,
                        )
                    );
                }
            }
        }

        $this->fill($data)->save();
        $this->unsetRelations();
    }

    public function hasType(ProductFilterTypeName ...$types): bool
    {
        $typeNames = [];

        foreach ($types as $type) {
            $typeNames[] = $type->value();
        }

        return in_array($this->type->name, $typeNames);
    }

    public function aggregate()
    {
        switch (true) {
            case $this->hasType(ProductFilterTypeName::Runtime):
                return  [
                    'terms' => ['field' => $this->field, 'size' =>  100]
                ];
            case $this->hasType(ProductFilterTypeName::Range):
                $ranges = [];

                /** @var ProductFilterValue $value */
                foreach ($this->values as $value) {
                    $ranges[] = array_merge($value->search_value, ['key' => $value->value]);
                }

                return [
                    'range' => ['field' => $this->field, 'ranges' => $ranges]
                ];
            case $this->hasType(ProductFilterTypeName::Exact):
                $exactFilters = [];

                /** @var ProductFilterValue $value */
                foreach ($this->values as $value) {
                    $exactFilters[$value->value] = [
                        'terms' => [$this->field => $value->search_value['terms']]
                    ];
                }

                return [
                    'filters' => ['filters' => $exactFilters]
                ];
        }
    }

    public function apply(array $query): array
    {
        switch (true) {
            case $this->hasType(ProductFilterTypeName::Runtime):
                $result =  [
                    'terms' => [
                        $this->field => $query,
                    ]
                ];
                break;
            case $this->hasType(ProductFilterTypeName::Range):
                $ranges = [];

                foreach ($query as $valueName) {
                    /** @var ProductFilterValue $filterValue */
                    $filterValue = $this->values()->where('value', '=', $valueName)->first();

                    if (! $filterValue) {
                        throw new HttpException(
                            Response::HTTP_NOT_FOUND,
                            'Filter value ' . $valueName . ' not found in filter ' . $this->name
                        );
                    }

                    $ranges[] = [
                        'range' => [$this->field => $filterValue->search_value]
                    ];
                }

                $result = [
                    'bool' => ['should' => $ranges]
                ];
                break;
            case $this->hasType(ProductFilterTypeName::Exact):
                $filterValues = [];

                foreach ($query as $valueName) {
                    /** @var ProductFilterValue $filterValue */
                    $filterValue = $this->values()->where('value', '=', $valueName)->first();

                    if (! $filterValue) {
                        throw new HttpException(
                            Response::HTTP_NOT_FOUND,
                            'Filter value ' . $valueName . ' not found in filter ' . $this->name
                        );
                    }

                    $filterValues = array_merge($filterValues, $filterValue->search_value['terms']);
                }

                $result = [
                    'terms' => [$this->field => $filterValues]
                ];
                break;
        }

        return $result;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductFilterType::class, 'product_filter_type_id', 'id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductFilterValue::class, 'product_filter_id', 'id');
    }
}

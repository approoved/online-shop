<?php

namespace App\Services\Elasticsearch\Repositories\Product\Serializers;

use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\ResourceNotFoundException;
use App\Models\ProductFilterValue\ProductFilterValue;
use App\Services\Elasticsearch\Repositories\Product\ProductSearchRepository;

final class RangeFilterRequestSerializer
{
    public function __construct(private readonly ProductSearchRepository $repository)
    {
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function serialize(ProductFilter $filter, array $query): array
    {
        $ranges = [];

        foreach ($query as $valueName) {
            /** @var ProductFilterValue $filterValue */
            $filterValue = $filter
                ->values()
                ->where('value', '=', $valueName)
                ->first();

            if (! $filterValue) {
                throw new ResourceNotFoundException(
                    sprintf(
                        'Filter value \'%s\' not found in filter \'%s\'.',
                        $valueName,
                        $filter->name
                    )
                );
            }

            $ranges[] = [
                'range' => [
                    $this->repository->getSearchField($filter->field) => $filterValue->search_value,
                ],
            ];
        }

        return [
            'bool' => ['should' => $ranges],
        ];
    }
}

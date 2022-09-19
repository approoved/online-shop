<?php

namespace App\Services\Elasticsearch\Repositories\Product\Serializers;

use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\ResourceNotFoundException;
use App\Models\ProductFilterValue\ProductFilterValue;

class ExactFilterRequestSerializer
{
    /**
     * @throws ResourceNotFoundException
     */
    public static function serialize(ProductFilter $filter, array $query): array
    {
        $terms = [];

        foreach ($query as $valueName) {
            /** @var ProductFilterValue $filterValue */
            $filterValue = $filter
                ->values()
                ->where('value', '=', $valueName)
                ->first();

            if (! $filterValue) {
                throw new ResourceNotFoundException(
                    sprintf('Filter value \'%s\' not found in filter \'%s\'.',
                        $valueName,
                        $filter->name
                    )
                );
            }

            $terms = array_merge($terms, $filterValue->search_value['terms']);
        }

        return [
            'terms' => [$filter->field->getField() => $terms]
        ];
    }
}

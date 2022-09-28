<?php

namespace App\Services\Elasticsearch\Repositories\Product\Serializers;

use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidAppConfigurationException;
use App\Models\ProductFilterType\ProductFilterTypeName;

final class FilterRequestSerializer
{
    /**
     * @throws InvalidAppConfigurationException
     */
    public function serialize(ProductFilter $filter, array $query): array
    {
        if (! isset($this->getFilterTypeSerializerMatch()[$filter->type->name])) {
            throw new InvalidAppConfigurationException(
                sprintf(
                    'Filter request serializer is not configurated for %s filter type.',
                    $filter->type->name
                )
            );
        }

        $serializer = resolve($this->getFilterTypeSerializerMatch()[$filter->type->name]);

        if (! method_exists($serializer, 'serialize')) {
            throw new InvalidAppConfigurationException(
                sprintf(
                    'Method \'serialize\' not declared. Class - %s.',
                    $serializer
                )
            );
        }

        return $serializer->serialize($filter, $query);
    }

    private function getFilterTypeSerializerMatch(): array
    {
        return [
            ProductFilterTypeName::Runtime->value() => RuntimeFilterRequestSerializer::class,
            ProductFilterTypeName::Range->value() => RangeFilterRequestSerializer::class,
            ProductFilterTypeName::Exact->value() => ExactFilterRequestSerializer::class,
        ];
    }
}

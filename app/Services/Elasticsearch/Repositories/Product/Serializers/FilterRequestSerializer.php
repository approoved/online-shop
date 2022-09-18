<?php

namespace App\Services\Elasticsearch\Repositories\Product\Serializers;

use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidAppConfigurationException;
use App\Models\ProductFilterType\ProductFilterTypeName;

class FilterRequestSerializer
{
    /**
     * @throws InvalidAppConfigurationException
     */
    public static function serialize(ProductFilter $filter, array $query): array
    {
        if (! isset(self::getFilterTypeSerializerMatch()[$filter->type->name])) {
            throw new InvalidAppConfigurationException(
                sprintf(
                    'Filter request serializer is not configurated for %s filter type.',
                    $filter->type->name
                )
            );
        }

        $serializer = self::getFilterTypeSerializerMatch()[$filter->type->name];

        if (! method_exists($serializer, 'serialize')) {
            throw new InvalidAppConfigurationException(
                sprintf(
                    'Method \'serialize\' not declared. Class - %s.',
                    $serializer
                )
            );
        }

        return call_user_func($serializer . '::serialize', $filter, $query);
    }

    private static function getFilterTypeSerializerMatch(): array
    {
        return [
            ProductFilterTypeName::Runtime->value() => RuntimeFilterRequestSerializer::class,
            ProductFilterTypeName::Range->value() => RangeFilterRequestSerializer::class,
            ProductFilterTypeName::Exact->value() => ExactFilterRequestSerializer::class
        ];
    }
}

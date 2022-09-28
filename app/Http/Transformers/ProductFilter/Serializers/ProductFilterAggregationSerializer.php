<?php

namespace App\Http\Transformers\ProductFilter\Serializers;

use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidAppConfigurationException;
use App\Models\ProductFilterType\ProductFilterTypeName;

final class ProductFilterAggregationSerializer
{
    /**
     * @throws InvalidAppConfigurationException
     */
    public static function serialize(ProductFilter $filter): array
    {
        if (! isset(self::getTypeSerializerMatch()[$filter->type->name])) {
            throw new InvalidAppConfigurationException(
                sprintf(
                    'Filter serializer is not configurated for %s filter type.',
                    $filter->type->name
                )
            );
        }

        $serializer = self::getTypeSerializerMatch()[$filter->type->name];

        if (! method_exists($serializer, 'serialize')) {
            throw new InvalidAppConfigurationException(
                sprintf(
                    'Method \'serialize\' not declared. Class - %s.',
                    $serializer
                )
            );
        }

        return call_user_func($serializer . '::serialize', $filter);
    }

    private static function getTypeSerializerMatch(): array
    {
        return [
            ProductFilterTypeName::Runtime->value() => RuntimeProductFilterAggregationSerializer::class,
            ProductFilterTypeName::Range->value() => RangeProductFilterAggregationSerializer::class,
            ProductFilterTypeName::Exact->value() => ExactProductFilterAggregationSerializer::class,
        ];
    }
}

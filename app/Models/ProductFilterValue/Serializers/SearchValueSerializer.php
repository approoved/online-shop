<?php

namespace App\Models\ProductFilterValue\Serializers;

use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidAppConfigurationException;
use App\Models\ProductFilterType\ProductFilterTypeName;

class SearchValueSerializer
{
    /**
     * @throws InvalidAppConfigurationException
     */
    public static function serialize(ProductFilter $filter, array $searchValue): array
    {
        if (! isset(static::getFilterTypeSerializerMatch()[$filter->type->name])) {
            throw new InvalidAppConfigurationException(
                sprintf(
                    'Search value serializer not configurated for %s filter value.',
                    $filter->type->name
                )
            );
        }

        $serializer = static::getFilterTypeSerializerMatch()[$filter->type->name];

        if (! method_exists($serializer, 'serialize')) {
            throw new InvalidAppConfigurationException(
                sprintf(
                    'Method \'serialize\' not declared. Class - %s.',
                    $serializer
                )
            );
        }

        return call_user_func($serializer . '::serialize', $filter, $searchValue);
    }

    private static function getFilterTypeSerializerMatch(): array
    {
        return [
            ProductFilterTypeName::Range->value() => RangeSearchValueSerializer::class,
            ProductFilterTypeName::Exact->value() => ExactSearchValueSerializer::class,
        ];
    }
}

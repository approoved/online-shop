<?php

namespace App\Http\Transformers\ProductFilter\Serializers;

use App\Models\ProductDetail\ProductDetail;
use App\Models\ProductFilter\ProductFilter;

class RuntimeProductFilterAggregationSerializer
{
    public static function serialize(ProductFilter $filter): array
    {
        $aggregation = [];

        /** @var ProductDetail $detail */
        foreach ($filter->details as $detail) {
            if (! isset($aggregation[(string) $detail->value])) {
                $aggregation[(string) $detail->value] = [
                    'value' => $detail->value,
                    'count' => 0,
                ];
            }

            $aggregation[(string) $detail->value]['count']++;
        }

        return array_values($aggregation);
    }
}

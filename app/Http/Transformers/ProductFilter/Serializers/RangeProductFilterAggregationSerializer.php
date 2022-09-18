<?php

namespace App\Http\Transformers\ProductFilter\Serializers;

use App\Models\ProductDetail\ProductDetail;
use App\Models\ProductFilter\ProductFilter;
use App\Models\ProductFilterValue\ProductFilterValue;

class RangeProductFilterAggregationSerializer
{
    public static function serialize(ProductFilter $filter): array
    {
        $aggregation = [];

        /** @var ProductDetail $detail */
        foreach ($filter->details as $detail) {
            /** @var ProductFilterValue $value */
            foreach ($filter->values as $value) {
                if (! isset($aggregation[$value->value])) {
                    $aggregation[$value->value] = [
                        'value' => $value->value,
                        'count' => 0
                    ];
                }

                if (isset($value->search_value['from'])) {
                    if ($detail->value < $value->search_value['from']) {
                        continue;
                    }
                }

                if (isset($value->search_value['to'])) {
                    if ($detail->value > $value->search_value['to']) {
                        continue;
                    }
                }

                $aggregation[$value->value]['count']++;
            }
        }

        return array_values($aggregation);
    }
}

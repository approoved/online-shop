<?php

namespace App\Http\Transformers\ProductFilter\Serializers;

use App\Models\ProductDetail\ProductDetail;
use App\Models\ProductFilter\ProductFilter;
use App\Models\ProductFilterValue\ProductFilterValue;

final class ExactProductFilterAggregationSerializer
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
                        'count' => 0,
                    ];
                }

                if (! in_array($detail->value, $value->search_value['terms'])) {
                    continue;
                }

                $aggregation[$value->value]['count']++;
            }
        }

        return array_values($aggregation);
    }
}

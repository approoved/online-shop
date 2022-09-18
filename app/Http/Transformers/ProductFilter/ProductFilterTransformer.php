<?php

namespace App\Http\Transformers\ProductFilter;

use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;
use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidAppConfigurationException;
use App\Http\Transformers\ProductFilter\Serializers\ProductFilterAggregationSerializer;

class ProductFilterTransformer extends BaseTransformer
{
    /**
     * @param Model&ProductFilter $model
     * @return array
     * @throws InvalidAppConfigurationException
     */
    public function transform(Model $model): array
    {
        $result = [
            'id' => $model->id,
            'name' => $model->name,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];

        if (in_array('aggregated-values', $this->appends)) {
            $result['aggregated-values'] = ProductFilterAggregationSerializer::serialize($model);
        }

        return $result;
    }
}

<?php

namespace App\Http\Transformers\ProductFilterValue;

use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;
use App\Models\ProductFilterValue\ProductFilterValue;

class ProductFilterValueTransformer extends BaseTransformer
{
    /**
     * @param Model&ProductFilterValue $model
     * @return array
     */
    public function transform(Model $model): array
    {
        return [
            'id' => $model->id,
            'value' => $model->value,
            'search_value' => $model->search_value,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}

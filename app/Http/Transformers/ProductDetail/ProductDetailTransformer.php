<?php

namespace App\Http\Transformers\ProductDetail;

use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;
use App\Models\ProductDetail\ProductDetail;

final class ProductDetailTransformer extends BaseTransformer
{
    /**
     * @param Model&ProductDetail $model
     */
    public function transform(Model $model): array
    {
        return [
            'id' => $model->id,
            'value' => $model->value,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}

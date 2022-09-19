<?php

namespace App\Http\Transformers\ProductFilterType;

use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;
use App\Models\ProductFilterType\ProductFilterType;

final class ProductFilterTypeTransformer extends BaseTransformer
{
    /**
     * @param Model&ProductFilterType $model
     */
    public function transform(Model $model): array
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}

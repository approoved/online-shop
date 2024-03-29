<?php

namespace App\Http\Transformers\ProductField;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductField\ProductField;
use App\Http\Transformers\BaseTransformer;

final class ProductFieldTransformer extends BaseTransformer
{
    /**
     * @param Model&ProductField $model
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

<?php

namespace App\Http\Transformers\ProductFieldGroup;

use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;
use App\Models\ProductFieldGroup\ProductFieldGroup;

final class ProductFieldGroupTransformer extends BaseTransformer
{
    /**
     * @param Model&ProductFieldGroup $model
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

<?php

namespace App\Http\Transformers\FieldType;

use App\Models\FieldType\FieldType;
use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;

class FieldTypeTransformer extends BaseTransformer
{
    /**
     * @param Model&FieldType $model
     * @return array
     */
    public function transform(Model $model): array
    {
        return [
            'id' => $model->id,
            'name' =>  $model->name,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}

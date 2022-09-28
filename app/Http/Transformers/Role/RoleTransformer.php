<?php

namespace App\Http\Transformers\Role;

use App\Models\Role\Role;
use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;

final class RoleTransformer extends BaseTransformer
{
    /**
     * @param Model&Role $model
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

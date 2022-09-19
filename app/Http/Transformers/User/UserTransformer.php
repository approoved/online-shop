<?php

namespace App\Http\Transformers\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;

final class UserTransformer extends BaseTransformer
{
    /**
     * @param Model&User $model
     */
    public function transform(Model $model): array
    {
        return [
            'id' => $model->id,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'email' => $model->email,
            'email_verified_at' => $model->email_verified_at,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}

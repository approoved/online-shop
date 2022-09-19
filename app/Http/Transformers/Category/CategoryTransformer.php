<?php

namespace App\Http\Transformers\Category;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;

final class CategoryTransformer extends BaseTransformer
{
    /**
     * @param Model&Category $model
     */
    public function transform(Model $model): array
    {
        if (in_array('ancestors', $this->appends)) {
            return $model->appendAncestors()->toArray();
        }

        return [
            'id' => $model->id,
            'name' => $model->name,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}

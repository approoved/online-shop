<?php

namespace App\Http\Transformers;

use App\Models\Category\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'parent_id' => $category->parent_id,
            'position' => $category->position,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    }
}

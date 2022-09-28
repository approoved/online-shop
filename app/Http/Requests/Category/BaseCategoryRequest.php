<?php

namespace App\Http\Requests\Category;

use App\Models\Category\Category;
use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\ValidationFields\IncludeRules;

abstract class BaseCategoryRequest extends BaseFormRequest
{
    public function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return array_merge(
            IncludeRules::getRules(Category::class),
            [
                'name' => [$required, 'string'],
                'parent_id' => [
                    'sometimes',
                    'integer',
                    'numeric',
                    'bail',
                    'exists:categories,id',
                ],
            ],
        );
    }
}

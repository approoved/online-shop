<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseFormRequest;

abstract class BaseCategoryRequest extends BaseFormRequest
{
    public function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'string'],
            'parent_id' => [
                'sometimes',
                'integer',
                'numeric',
                'bail',
                'exists:categories,id',
            ],
        ];
    }
}

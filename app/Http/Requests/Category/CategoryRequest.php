<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseFormRequest;

final class CategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],
        ];
    }
}

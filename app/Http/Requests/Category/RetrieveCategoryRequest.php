<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseFormRequest;

final class RetrieveCategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'serialize' => ['sometimes', 'string'],
            'append' => ['sometimes', 'string']
        ];
    }
}

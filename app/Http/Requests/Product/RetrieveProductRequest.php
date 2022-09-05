<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseFormRequest;
use App\Http\Controllers\ProductController;

class RetrieveProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'append' => ['sometimes', 'string'],
            'query' => ['sometimes', 'string'],
            'category_id' => [
                'sometimes',
                'bail',
                'integer',
                'numeric',
                'exists:categories,id',
                'required_with:filter',
            ],
            'filter' => ['sometimes', 'array'],
            'per_page' => [
                'sometimes',
                'integer',
                'numeric',
                'min:1',
                'max:' . ProductController::getMaxPerPage(),
            ],
            'page' => ['sometimes', 'integer', 'numeric', 'min:1'],
        ];
    }
}

<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseFormRequest;
use App\Http\Controllers\ProductController;
use App\Http\Requests\ValidationFields\IncludeRules;
use App\Models\Product\Product;

final class GetProductListRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(
            IncludeRules::getRules(Product::class),
            [
            'append' => ['sometimes', 'string', 'values_in:short-details'],
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
            ]
        );
    }
}

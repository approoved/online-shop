<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseFormRequest;
use App\Http\Controllers\ProductController;
use App\Http\Requests\ValidationFields\IncludeRules;
use App\Models\Product\Product;

final class GetProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(
            IncludeRules::getRules(Product::class),
            [
                'append' => ['sometimes', 'string', 'values_in:short-details'],
            ]
        );
    }
}

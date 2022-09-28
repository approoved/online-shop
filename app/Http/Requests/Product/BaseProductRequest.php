<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\ValidationFields\IncludeRules;
use App\Models\Product\Product;

abstract class BaseProductRequest extends BaseFormRequest
{
    protected function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return array_merge(
            IncludeRules::getRules(Product::class),
            [
                'sku' => [$required, 'string', 'unique:products,sku'],
                'name' => [$required, 'string'],
                'price' => [$required, 'numeric', 'min:0.01'],
                'add_quantity' => ['sometimes', 'integer'],
                'details' => ['sometimes'],
                'details.*' => ['required_with:details', 'array'],
                'details.*.value' => ['required_with:details'],
                'details.*.product_field_id' => [
                    'required_with:details',
                    'integer',
                    'numeric',
                    'bail',
                    'exists:product_fields,id',
                ],
            ]
        );
    }
}

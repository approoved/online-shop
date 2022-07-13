<?php

namespace App\Http\Requests\Product;

use App\Rules\Uppercase;
use App\Rules\ArrayWithStringIndices;
use App\Http\Requests\BaseFormRequest;

abstract class BaseProductRequest extends BaseFormRequest
{
    protected function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return  [
            'sku' => [
                $required,
                'string',
                new Uppercase(),
                'unique:products,sku',
            ],
            'name' => [
                $required,
                'string',
            ],
            'category' => [
                $required,
                'string',
            ],
            'price' => [
                $required,
                'numeric',
                'min:0.01',
            ],
            'add_quantity' => [
                'sometimes',
                'integer',
            ],
            'details' => [
                'sometimes',
                new ArrayWithStringIndices(),
            ],
        ];
    }
}

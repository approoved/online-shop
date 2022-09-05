<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseFormRequest;
use App\Http\Rules\ArrayWithIntegerIndices;

abstract class BaseProductRequest extends BaseFormRequest
{
    protected function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return  [
            'sku' => [
                $required,
                'string',
                'unique:products,sku',
            ],
            'name' => [
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
                new ArrayWithIntegerIndices(),
            ],
        ];
    }
}

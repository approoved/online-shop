<?php

namespace App\Http\Requests\ProductFilterValue;

use App\Http\Requests\BaseFormRequest;

abstract class BaseProductFilterValueRequest extends BaseFormRequest
{
    public function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return [
            'value' => [$required, 'string'],
            'search_value' => [$required, 'array'],
            'search_value.from' => ['numeric'],
            'search_value.to' => ['numeric'],
            'search_value.terms' => ['array'],
            'search_value.*' => ['sometimes'],
        ];
    }
}

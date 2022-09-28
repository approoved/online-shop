<?php

namespace App\Http\Requests\ProductFilterValue;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\ValidationFields\IncludeRules;
use App\Models\ProductFilterValue\ProductFilterValue;

abstract class BaseProductFilterValueRequest extends BaseFormRequest
{
    public function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return array_merge(
            IncludeRules::getRules(ProductFilterValue::class),
            [
                'value' => [$required, 'string'],
                'search_value' => [$required, 'array'],
                'search_value.from' => ['sometimes'],
                'search_value.to' => ['sometimes'],
                'search_value.terms' => ['sometimes', 'array'],
                'search_value.*' => ['sometimes'],
            ]
        );
    }
}

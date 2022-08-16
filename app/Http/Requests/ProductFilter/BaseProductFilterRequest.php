<?php

namespace App\Http\Requests\ProductFilter;

use App\Http\Requests\BaseFormRequest;

abstract class BaseProductFilterRequest extends BaseFormRequest
{
    protected function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        //TODO id

        return [
            'name' => [$required, 'string'],
            'field' => [$required, 'string'],
            'product_filter_type_id' => [$required, 'exists:product_filter_types,id'],
        ];
    }
}

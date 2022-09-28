<?php

namespace App\Http\Requests\ProductFilter;

use App\Http\Requests\BaseFormRequest;
use App\Models\ProductFilter\ProductFilter;
use App\Http\Requests\ValidationFields\IncludeRules;

abstract class BaseProductFilterRequest extends BaseFormRequest
{
    protected function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return array_merge(
            IncludeRules::getRules(ProductFilter::class),
            [
                'name' => [$required, 'string'],
                'product_field_id' => [
                    $required,
                    'integer',
                    'numeric',
                    'bail',
                    'exists:product_fields,id',
                ],
                'product_filter_type_id' => [
                    $required,
                    'integer',
                    'numeric',
                    'bail',
                    'exists:product_filter_types,id',
                ],
            ]
        );
    }
}

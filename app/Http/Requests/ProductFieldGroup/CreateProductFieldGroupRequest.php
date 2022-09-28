<?php

namespace App\Http\Requests\ProductFieldGroup;

use App\Http\Requests\BaseFormRequest;
use App\Models\ProductFieldGroup\ProductFieldGroup;
use App\Http\Requests\ValidationFields\IncludeRules;

final class CreateProductFieldGroupRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(
            IncludeRules::getRules(ProductFieldGroup::class),
            [
                'name' => [
                    'required',
                    'string',
                    'unique:product_field_groups,name',
                ],
            ]
        );
    }
}

<?php

namespace App\Http\Requests\ProductFieldGroup;

use App\Http\Requests\BaseFormRequest;

class CreateProductFieldGroupRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'unique:product_field_groups,name',
            ],
        ];
    }
}

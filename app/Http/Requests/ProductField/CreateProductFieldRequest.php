<?php

namespace App\Http\Requests\ProductField;

use App\Http\Requests\BaseFormRequest;

class CreateProductFieldRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'field_type_id' => [
                'required',
                'integer',
                'numeric',
                'bail',
                'exists:field_types,id',
            ],
        ];
    }
}

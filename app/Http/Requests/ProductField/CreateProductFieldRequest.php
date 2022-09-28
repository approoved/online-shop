<?php

namespace App\Http\Requests\ProductField;

use App\Http\Requests\BaseFormRequest;
use App\Models\ProductField\ProductField;
use App\Http\Requests\ValidationFields\IncludeRules;

final class CreateProductFieldRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(
            IncludeRules::getRules(ProductField::class),
            [
                'name' => ['required', 'string'],
                'field_type_id' => [
                    'required',
                    'integer',
                    'numeric',
                    'bail',
                    'exists:field_types,id',
                ],
            ]
        );
    }
}

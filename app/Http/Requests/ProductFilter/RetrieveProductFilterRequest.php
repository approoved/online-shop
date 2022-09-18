<?php

namespace App\Http\Requests\ProductFilter;

use App\Http\Requests\BaseFormRequest;

final class RetrieveProductFilterRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'append' => ['sometimes', 'string'],
        ];
    }
}

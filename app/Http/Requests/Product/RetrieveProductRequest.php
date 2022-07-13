<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseFormRequest;

class RetrieveProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'include' => [
                'string',
            ],
            'category' => [
                'string'
            ],
        ];
    }
}

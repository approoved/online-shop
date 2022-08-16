<?php

namespace App\Http\Requests\ProductFilter;

use App\Http\Requests\BaseFormRequest;

class GetFilterTypeListRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'field' => ['required', 'string'],
        ];
    }
}

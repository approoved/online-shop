<?php

namespace App\Http\Requests\ProductFilter;

use App\Http\Requests\BaseFormRequest;

final class GetFieldListRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'exclude' => ['string'],
        ];
    }
}

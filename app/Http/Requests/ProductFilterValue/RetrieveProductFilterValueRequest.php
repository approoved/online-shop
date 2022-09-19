<?php

namespace App\Http\Requests\ProductFilterValue;

use App\Http\Requests\BaseFormRequest;
use App\Http\Controllers\ProductFilterValueController;

final class RetrieveProductFilterValueRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:' . ProductFilterValueController::getMaxPerPage(),
            ],
        ];
    }
}

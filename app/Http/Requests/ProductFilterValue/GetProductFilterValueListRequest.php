<?php

namespace App\Http\Requests\ProductFilterValue;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\ValidationFields\IncludeRules;
use App\Models\ProductFilterValue\ProductFilterValue;
use App\Http\Controllers\ProductFilterValueController;

final class GetProductFilterValueListRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(
            IncludeRules::getRules(ProductFilterValue::class),
            [
                'page' => ['sometimes', 'integer', 'min:1'],
                'per_page' => [
                    'sometimes',
                    'integer',
                    'min:1',
                    'max:' . ProductFilterValueController::getMaxPerPage(),
                ],
            ]
        );
    }
}

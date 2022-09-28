<?php

namespace App\Http\Requests\ProductFilterValue;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\ValidationFields\IncludeRules;
use App\Models\ProductFilterValue\ProductFilterValue;

final class GetProductFilterValueRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return IncludeRules::getRules(ProductFilterValue::class);
    }
}

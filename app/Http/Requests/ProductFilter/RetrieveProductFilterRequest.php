<?php

namespace App\Http\Requests\ProductFilter;

use App\Http\Requests\BaseFormRequest;
use App\Models\ProductFilter\ProductFilter;
use App\Http\Requests\ValidationFields\IncludeRules;

final class RetrieveProductFilterRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(
            IncludeRules::getRules(ProductFilter::class),
            [
                'append' => ['sometimes', 'string', 'values_in:aggregated-values'],
            ]
        );
    }
}

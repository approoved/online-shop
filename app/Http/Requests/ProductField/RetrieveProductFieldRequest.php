<?php

namespace App\Http\Requests\ProductField;

use App\Http\Requests\BaseFormRequest;
use App\Models\ProductField\ProductField;
use App\Http\Requests\ValidationFields\IncludeRules;

class RetrieveProductFieldRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return IncludeRules::getRules(ProductField::class);
    }
}

<?php

namespace App\Http\Requests\ProductFieldGroup;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\ValidationFields\IncludeRules;
use App\Models\ProductFieldGroup\ProductFieldGroup;

class RetrieveProductFieldGroupRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return IncludeRules::getRules(ProductFieldGroup::class);
    }
}

<?php

namespace App\Http\Requests\Category;

use App\Models\Category\Category;
use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\ValidationFields\IncludeRules;

final class GetCategoryListRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(
            IncludeRules::getRules(Category::class),
            [
                'serialize' => ['sometimes', 'string', 'values_in:tree'],
            ]
        );
    }
}

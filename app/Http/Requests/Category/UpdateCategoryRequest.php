<?php

namespace App\Http\Requests\Category;

class UpdateCategoryRequest extends BaseCategoryRequest
{
    public function rules(): array
    {
        return $this->getRules(false);
    }
}

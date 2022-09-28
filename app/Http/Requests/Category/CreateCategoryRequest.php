<?php

namespace App\Http\Requests\Category;

final class CreateCategoryRequest extends BaseCategoryRequest
{
    public function rules(): array
    {
        return $this->getRules(true);
    }
}

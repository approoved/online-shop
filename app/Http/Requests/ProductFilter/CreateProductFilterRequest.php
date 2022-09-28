<?php

namespace App\Http\Requests\ProductFilter;

final class CreateProductFilterRequest extends BaseProductFilterRequest
{
    public function rules(): array
    {
        return $this->getRules(true);
    }
}

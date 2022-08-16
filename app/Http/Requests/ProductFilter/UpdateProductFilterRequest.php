<?php

namespace App\Http\Requests\ProductFilter;

final class UpdateProductFilterRequest extends BaseProductFilterRequest
{
    public function rules(): array
    {
        return $this->getRules(false);
    }
}

<?php

namespace App\Http\Requests\ProductFilterValue;

class CreateProductFilterValueRequest extends BaseProductFilterValueRequest
{
    public function rules(): array
    {
        return $this->getRules(true);
    }
}

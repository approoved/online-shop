<?php

namespace App\Http\Requests\ProductFilterValue;

class UpdateProductFilterValueRequest extends BaseProductFilterValueRequest
{
    public function rules(): array
    {
        return $this->getRules(false);
    }
}

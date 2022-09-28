<?php

namespace App\Http\Requests\ProductFilterValue;

final class UpdateProductFilterValueRequest extends BaseProductFilterValueRequest
{
    public function rules(): array
    {
        return $this->getRules(false);
    }
}

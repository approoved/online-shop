<?php

namespace App\Http\Requests\Product;

final class UpdateProductRequest extends BaseProductRequest
{
    public function rules(): array
    {
        return $this->getRules(false);
    }
}

<?php

namespace App\Http\Requests\Product;

final class CreateProductRequest extends BaseProductRequest
{
    public function rules(): array
    {
        return $this->getRules(true);
    }
}

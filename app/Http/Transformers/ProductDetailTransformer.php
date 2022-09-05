<?php

namespace App\Http\Transformers;

use App\Models\ProductDetail\ProductDetail;

class ProductDetailTransformer extends BaseTransformer
{
    public function transform(ProductDetail $detail): array
    {
        return [
            'id' => $detail->id,
            'product_field_id' => $detail->product_field_id,
            'product_id' => $detail->product_id,
            'value' => $detail->value,
            'created_at' => $detail->created_at,
            'updated_at' => $detail->updated_at
        ];
    }
}

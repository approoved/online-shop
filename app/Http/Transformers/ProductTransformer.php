<?php

namespace App\Http\Transformers;

use App\Models\Product\Product;
use App\Models\ProductDetail\ProductDetail;

class ProductTransformer extends BaseTransformer
{
    protected array $appends = [];

    public function transform(Product $product): array
    {
        $result = [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'category_id' => $product->category_id,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at
        ];

        if (in_array('short-details', $this->appends)) {
            $shortDetails = [];

            /** @var ProductDetail $detail */
            foreach ($product->details as $detail) {
                $shortDetails[$detail->field->group->name][$detail->field->name] = $detail->value;
            }

            $result['short_details'] = $shortDetails;
        }

        return $result;
    }
}

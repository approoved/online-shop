<?php

namespace App\Http\Transformers\Product;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use App\Http\Transformers\BaseTransformer;
use App\Models\ProductDetail\ProductDetail;

class ProductTransformer extends BaseTransformer
{
    /**
     * @param Model&Product $model
     * @return array
     */
    public function transform(Model $model): array
    {
        $result = [
            'id' => $model->id,
            'sku' => $model->sku,
            'name' => $model->name,
            'price' => $model->price,
            'quantity' => $model->quantity,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];

        if (in_array('short-details', $this->appends)) {
            $shortDetails = [];

            /** @var ProductDetail $detail */
            foreach ($model->details as $detail) {
                $shortDetails[$detail->field->group->name][$detail->field->name] = $detail->value;
            }

            $result['short_details'] = $shortDetails;
        }

        return $result;
    }
}

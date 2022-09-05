<?php

namespace App\Http\Transformers;

use App\Models\ProductField\ProductField;

class ProductFieldTransformer extends BaseTransformer
{
    public function transform(ProductField $field): array
    {
        return [
            'id' => $field->id,
            'name' => $field->name,
            'product_field_group_id' => $field->product_field_group_id,
            'field_type_id' => $field->field_type_id,
            'created_at' => $field->created_at,
            'updated_at' => $field->updated_at
        ];
    }
}

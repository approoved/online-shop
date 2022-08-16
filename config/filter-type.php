<?php

use App\Models\ProductFilter\ProductFilterTypeName;

return [
    'default' => [
        ProductFilterTypeName::Runtime->value(),
        ProductFilterTypeName::Range->value(),
        ProductFilterTypeName::Exact->value(),
    ],
    'text' => [
        ProductFilterTypeName::Runtime->value(),
        ProductFilterTypeName::Exact->value(),
    ],
];

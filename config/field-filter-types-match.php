<?php

use App\Models\FieldType\FieldTypeName;
use App\Models\ProductFilterType\ProductFilterTypeName;

return [
    FieldTypeName::Integer->value() => [
        ProductFilterTypeName::Runtime,
        ProductFilterTypeName::Range,
        ProductFilterTypeName::Exact,
    ],
    FieldTypeName::Float->value() => [
        ProductFilterTypeName::Runtime,
        ProductFilterTypeName::Range,
        ProductFilterTypeName::Exact,
    ],
    FieldTypeName::Date->value() => [
        ProductFilterTypeName::Runtime,
        ProductFilterTypeName::Range,
        ProductFilterTypeName::Exact,
    ],
    FieldTypeName::Text->value() => [
        ProductFilterTypeName::Runtime,
        ProductFilterTypeName::Exact,
    ],
];

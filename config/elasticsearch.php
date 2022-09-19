<?php

use App\Models\FieldType\FieldTypeName;

return [
    'client' => [
        'username' => env('ELASTIC_USERNAME'),
        'password' => env('ELASTIC_PASSWORD'),
        'hosts' => env('ELASTIC_HOSTS'),
        'ca_cert' => env('ELASTIC_CA_CERT'),
    ],
    'indices_settings' => [
        'products' => [
            'mappings' => [
                ['field' => 'id', 'type' => FieldTypeName::Integer],
                ['field' => 'sku', 'type' => FieldTypeName::Text],
                ['field' => 'name', 'type' => FieldTypeName::Text],
                ['field' => 'category_id', 'type' => FieldTypeName::Integer],
                ['field' => 'price', 'type' => FieldTypeName::Float],
                ['field' => 'quantity', 'type' => FieldTypeName::Integer],
                ['field' => 'created_at', 'type' => FieldTypeName::Date],
                ['field' => 'updated_at', 'type' => FieldTypeName::Date],
            ]
        ],
    ]
];

<?php

use App\Models\ProductField\FieldTypeName;

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
                ['name' => 'id', 'type' => FieldTypeName::integer],
                ['name' => 'sku', 'type' => FieldTypeName::text],
                ['name' => 'name', 'type' => FieldTypeName::text],
                ['name' => 'category_id', 'type' => FieldTypeName::integer],
                ['name' => 'price', 'type' => FieldTypeName::float],
                ['name' => 'quantity', 'type' => FieldTypeName::integer],
            ]
        ],
    ]
];

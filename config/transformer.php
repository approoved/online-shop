<?php

use App\Models\Product\Product;
use App\Models\Category\Category;
use App\Models\ProductField\ProductField;
use App\Models\ProductDetail\ProductDetail;
use App\Http\Transformers\ProductTransformer;
use App\Http\Transformers\CategoryTransformer;
use App\Http\Transformers\ProductFieldTransformer;
use App\Http\Transformers\ProductDetailTransformer;

return [
    Product::class => ProductTransformer::class,
    ProductDetail::class => ProductDetailTransformer::class,
    ProductField::class => ProductFieldTransformer::class,
    Category::class => CategoryTransformer::class
];

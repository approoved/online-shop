<?php

use App\Models\Role\Role;
use App\Models\User\User;
use App\Models\Product\Product;
use App\Models\Category\Category;
use App\Models\FieldType\FieldType;
use App\Models\ProductField\ProductField;
use App\Models\ProductDetail\ProductDetail;
use App\Models\ProductFilter\ProductFilter;
use App\Http\Transformers\Role\RoleTransformer;
use App\Http\Transformers\User\UserTransformer;
use App\Models\ProductFieldGroup\ProductFieldGroup;
use App\Models\ProductFilterType\ProductFilterType;
use App\Models\ProductFilterValue\ProductFilterValue;
use App\Http\Transformers\Product\ProductTransformer;
use App\Http\Transformers\Category\CategoryTransformer;
use App\Http\Transformers\FieldType\FieldTypeTransformer;
use App\Http\Transformers\ProductField\ProductFieldTransformer;
use App\Http\Transformers\ProductDetail\ProductDetailTransformer;
use App\Http\Transformers\ProductFilter\ProductFilterTransformer;
use App\Http\Transformers\ProductFieldGroup\ProductFieldGroupTransformer;
use App\Http\Transformers\ProductFilterType\ProductFilterTypeTransformer;
use App\Http\Transformers\ProductFilterValue\ProductFilterValueTransformer;

return [
    User::class => UserTransformer::class,
    Role::class => RoleTransformer::class,
    Category::class => CategoryTransformer::class,
    ProductFieldGroup::class => ProductFieldGroupTransformer::class,
    FieldType::class => FieldTypeTransformer::class,
    ProductField::class => ProductFieldTransformer::class,
    Product::class => ProductTransformer::class,
    ProductDetail::class => ProductDetailTransformer::class,
    ProductFilter::class => ProductFilterTransformer::class,
    ProductFilterValue::class => ProductFilterValueTransformer::class,
    ProductFilterType::class => ProductFilterTypeTransformer::class,
];

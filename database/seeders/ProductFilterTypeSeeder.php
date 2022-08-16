<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductFilter\ProductFilterType;
use App\Models\ProductFilter\ProductFilterTypeName;

class ProductFilterTypeSeeder extends Seeder
{
    public function run(): void
    {
        /** @var ProductFilterTypeName $typeName */
        foreach (ProductFilterTypeName::getList() as $typeName) {
            ProductFilterType::query()->updateOrCreate([
                'name' => $typeName->value()
            ]);
        }
    }
}

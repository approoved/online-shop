<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductFilterType\ProductFilterType;
use App\Models\ProductFilterType\ProductFilterTypeName;

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

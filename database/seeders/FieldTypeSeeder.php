<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductField\FieldType;
use App\Models\ProductField\FieldTypeName;

class FieldTypeSeeder extends Seeder
{
    public function run(): void
    {
        /** @var FieldTypeName $typeName */
        foreach (FieldTypeName::getList() as $typeName) {
            FieldType::query()->updateOrCreate([
                'name' => $typeName->value()
            ]);
        }
    }
}

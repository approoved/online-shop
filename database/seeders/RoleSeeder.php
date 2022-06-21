<?php

namespace Database\Seeders;

use App\Models\Role\Role;
use App\Models\Role\RoleName;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RoleName::getList() as $role) {
            Role::query()->updateOrCreate([
                'name' => $role->value(),
            ]);
        }
    }
}

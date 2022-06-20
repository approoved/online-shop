<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Role::ROLE_LIST as $role) {
            Role::query()->updateOrCreate(['name' => $role]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::factory()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'Customer'],
                ['name' => 'Manager'],
                ['name' => 'Administrator']
            ))
            ->create();
    }
}

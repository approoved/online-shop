<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Role\Role;
use App\Models\User\User;
use App\Models\Role\RoleName;
use Illuminate\Database\Seeder;

class DefaultAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Role $adminRole */
        $adminRole = Role::query()
            ->firstWhere('name', '=', RoleName::Admin->value());

        /** @var User $admin */
        $admin = User::query()
            ->firstWhere('role_id', '=', $adminRole->id);

        if (! $admin) {
            User::query()->create([
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => config('app.default_admin.email'),
                'password' => bcrypt(config('app.default_admin.password')),
                'email_verified_at' => Carbon::now(),
                'role_id' => $adminRole->id,
            ]);
        }
    }
}

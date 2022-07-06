<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Role\Role;
use App\Models\Role\RoleName;
use Illuminate\Database\Seeder;

class DefaultAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        /** @var User $admin */
        $admin = User::query()
            ->firstWhere('email', '=', config('app.default_admin.email'));

        if (! $admin) {
            /** @var Role $adminRole */
            $adminRole = Role::query()
                ->firstWhere('name', '=', RoleName::admin->value());

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

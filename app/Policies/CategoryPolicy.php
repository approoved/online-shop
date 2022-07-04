<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role\Role;
use App\Models\Role\RoleName;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public const STORE = 'store';
    public const UPDATE = 'update';
    public const DESTROY = 'destroy';

    /**
     * @param Authenticatable&User $user
     */
    public function store(Authenticatable $user): bool
    {
        return $this->manage($user);
    }

    public function update(Authenticatable $user): bool
    {
        return $this->manage($user);
    }

    public function destroy(Authenticatable $user): bool
    {
        return $this->manage($user);
    }

    /**
     * @param Authenticatable&User $user
     */
    private function manage(Authenticatable $user): bool
    {
        /** @var Role $admin */
        $admin = Role::query()
            ->firstWhere('name', '=', RoleName::admin->value());

        /** @var Role $manager */
        $manager = Role::query()
            ->firstWhere('name', '=', RoleName::manager->value());

        return $user->role_id === $manager->id || $user->role_id === $admin->id;
    }
}

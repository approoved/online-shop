<?php

namespace App\Policies;

use App\Models\User\User;
use App\Models\Role\RoleName;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductFilterPolicy
{
    use HandlesAuthorization;

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }

    public function update(User $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }
}

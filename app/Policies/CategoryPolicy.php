<?php

namespace App\Policies;

use App\Models\User\User;
use App\Models\Role\RoleName;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Access\HandlesAuthorization;

final class CategoryPolicy
{
    use HandlesAuthorization;

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    /**
     * @param Authenticatable&User $user
     */
    public function create(Authenticatable $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }

    /**
     * @param Authenticatable&User $user
     */
    public function update(Authenticatable $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }

    /**
     * @param Authenticatable&User $user
     */
    public function delete(Authenticatable $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }
}

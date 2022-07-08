<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role\RoleName;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public const INDEX = 'index';
    public const SHOW = 'show';
    public const UPDATE = 'update';
    public const UPDATE_ROLE = 'updateRole';
    public const DESTROY = 'destroy';

    /**
     * @param Authenticatable&User $user
     */
    public function index(Authenticatable $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }

    /**
     * @param Authenticatable&User $authUser
     */
    public function show(Authenticatable $authUser, User $user): bool
    {
        return $authUser->id === $user->id ||
            $authUser->hasRole(RoleName::admin, RoleName::manager);
    }

    /**
     * @param Authenticatable&User $authUser
     */
    public function update(Authenticatable $authUser, User $user): bool
    {
        return $authUser->id === $user->id;
    }

    /**
     * @param Authenticatable&User $authUser
     */
    public function updateRole(Authenticatable $authUser, User $user): bool
    {
        return $authUser->hasRole(RoleName::admin) &&
            $authUser->id !== $user->id;
    }

    /**
     * @param Authenticatable&User $authUser
     */
    public function destroy(Authenticatable $authUser, User $user): bool
    {
        return $authUser->id === $user->id;
    }
}

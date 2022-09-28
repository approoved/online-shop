<?php

namespace App\Policies;

use App\Models\User\User;
use App\Models\Role\RoleName;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Access\HandlesAuthorization;

final class UserPolicy
{
    use HandlesAuthorization;

    public const VIEW_ANY = 'viewAny';

    public const VIEW = 'view';

    public const UPDATE = 'update';

    public const UPDATE_ROLE = 'updateRole';

    public const DELETE = 'delete';

    /**
     * @param Authenticatable&User $user
     */
    public function viewAny(Authenticatable $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }

    /**
     * @param Authenticatable&User $authUser
     */
    public function view(Authenticatable $authUser, User $user): bool
    {
        return $authUser->id === $user->id
            || $authUser->hasRole(RoleName::Admin, RoleName::Manager);
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
        return $authUser->hasRole(RoleName::Admin)
            && $authUser->id !== $user->id;
    }

    /**
     * @param Authenticatable&User $authUser
     */
    public function delete(Authenticatable $authUser, User $user): bool
    {
        return $authUser->id === $user->id;
    }
}

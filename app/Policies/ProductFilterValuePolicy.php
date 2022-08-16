<?php

namespace App\Policies;

use App\Models\Role\RoleName;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class ProductFilterValuePolicy
{
    use HandlesAuthorization;

    public const VIEW_ANY = 'viewAny';
    public const VIEW = 'view';
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }

    public function view(User $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }

    public function update(User $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }
}

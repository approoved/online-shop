<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role\RoleName;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductFieldPolicy
{
    use HandlesAuthorization;

    public const CREATE = 'create';
    public const VIEW_ANY = 'viewAny';
    public const VIEW = 'view';
    public const DELETE = 'delete';

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }

    public function view(User $user): bool
    {
        return $user->hasRole(RoleName::admin, RoleName::manager);
    }
}

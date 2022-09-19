<?php

namespace App\Policies;

use App\Models\User\User;
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
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }

    public function view(User $user): bool
    {
        return $user->hasRole(RoleName::Admin, RoleName::Manager);
    }
}

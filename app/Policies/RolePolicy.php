<?php

namespace App\Policies;

use App\Models\User\User;
use App\Models\Role\RoleName;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Access\HandlesAuthorization;

final class RolePolicy
{
    use HandlesAuthorization;

    public const VIEW_ANY = 'viewAny';

    /**
     * @param Authenticatable&User $user
     */
    public function viewAny(Authenticatable $user): bool
    {
        return $user->hasRole(RoleName::Admin);
    }
}

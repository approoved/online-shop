<?php

namespace App\Http\Controllers;

use App\Models\Role\Role;
use App\Policies\RolePolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;

final class GetRoleListController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function __invoke(): JsonResponse
    {
        $this->authorize(RolePolicy::VIEW_ANY, Role::class);

        $roles = Role::getSearchQuery()->get();

        return $this->transformToJson($roles);
    }
}

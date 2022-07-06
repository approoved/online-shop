<?php

namespace App\Http\Controllers;

use App\Models\Role\Role;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetRoleListController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(Role::all());
    }
}

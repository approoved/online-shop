<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role\Role;
use Illuminate\Support\Str;
use App\Models\Role\RoleName;
use App\Http\Requests\CreateUserRequest;
use App\Notifications\EmailVerification;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserController extends Controller
{
    public function store(CreateUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var Role $customerRole */
        $customerRole = Role::query()
            ->firstWhere('name', '=', RoleName::customer->value());

        $data['password'] = bcrypt($data['password']);
        $data['token'] = Str::random(72);
        $data['role_id'] = $customerRole->id;

        /** @var User $user */
        $user = User::query()->create($data);

        $notification = new EmailVerification($user);
        $user->notify($notification);

        return response()->json($user, Response::HTTP_CREATED);
    }
}

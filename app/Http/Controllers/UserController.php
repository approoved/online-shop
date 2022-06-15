<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use App\Notifications\EmailVerification;
use App\Http\Requests\CreateUserRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserController extends Controller
{
    public function store(CreateUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var Role $customerRole */
        $customerRole = Role::query()
            ->firstWhere('name', '=', 'Customer');

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

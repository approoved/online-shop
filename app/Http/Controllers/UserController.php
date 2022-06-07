<?php

namespace App\Http\Controllers;

use App\Models\User;
use illuminate\Support\Str;
use App\Notifications\EmailVerification;
use App\Http\Requests\CreateUserRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserController extends Controller
{
    public function store(CreateUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);
        $data['token'] = Str::random(72);

        $user = new User();
        $user->fill($data)->save();

        $notification = new EmailVerification($user);
        $user->notify($notification);

        return response()->json($user, Response::HTTP_CREATED);
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UserVerificationRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class AuthController extends Controller
{
    public function verify(UserVerificationRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User $user */
        $user = User::query()
            ->where('token', '=', $data['token'])
            ->firstOrFail();

        $user->update([
            'email_verified_at' => Carbon::now(),
            'token' => null,
        ]);

        return response()->json($user);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User $user */
        $user = User::query()
            ->where('email', '=', $data['email'])
            ->firstOrFail();

        if (! Hash::check($data['password'], $user->password)) {
            throw new HttpException(Response::HTTP_NOT_FOUND);
        }

        $token = $user->createToken('API token')->accessToken;

        return response()->json(['user' => $user, 'token' => $token], Response::HTTP_CREATED);
    }

    /**
     * @param Authenticatable&User $user
     */
    public function logout(Authenticatable $user): Response
    {
        $user->token()->revoke();

        return response()->noContent();
    }
}

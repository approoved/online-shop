<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\Auth\UserVerificationRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class AuthController extends Controller
{
    public function verify(UserVerificationRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User $user */
        $user = User::getSearchQuery()
            ->where('token', '=', $data['token'])
            ->firstOrFail();

        $user->update([
            'email_verified_at' => Carbon::now(),
            'token' => null,
        ]);

        return response()->json($this->transform($user));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User $user */
        $user = User::getSearchQuery()
            ->where('email', '=', $data['email'])
            ->firstOrFail();

        if (! Hash::check($data['password'], $user->password)) {
            throw new HttpException(Response::HTTP_NOT_FOUND);
        }

        $token = $user->createToken('API token')->accessToken;

        return response()->json([
            'user' => $this->transform($user),
            'token' => $token
        ], Response::HTTP_CREATED);
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

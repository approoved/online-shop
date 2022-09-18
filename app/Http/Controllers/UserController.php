<?php

namespace App\Http\Controllers;

use App\Models\Role\Role;
use App\Models\User\User;
use Illuminate\Support\Str;
use App\Policies\UserPolicy;
use App\Models\Role\RoleName;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmailVerification;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class UserController extends Controller
{
    public function store(CreateUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var Role $customerRole */
        $customerRole = Role::query()
            ->firstWhere('name', RoleName::Customer->value());

        $data['password'] = bcrypt($data['password']);
        $data['token'] = Str::random(72);
        $data['role_id'] = $customerRole->id;

        /** @var User $user */
        $user = User::query()->create($data);

        $notification = new EmailVerification($user);
        $user->notify($notification);

        return response()->json($this->transform($user), Response::HTTP_CREATED);
    }

    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize(UserPolicy::VIEW_ANY, User::class);

        $users = User::getSearchQuery()
            ->paginate();

        return response()->json($this->transform($users));
    }

    /**
     * @throws AuthorizationException
     */
    public function show(int $userId): JsonResponse
    {
        /** @var User $user */
        $user = User::getSearchQuery()
            ->where('id', $userId)
            ->firstOrFail();

        $this->authorize(UserPolicy::VIEW, $user);

        return response()->json($this->transform($user));
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['role'])) {
            $this->authorize(UserPolicy::UPDATE_ROLE, $user);

            /** @var Role $role */
            $role = Role::query()
                ->where('name', '=', RoleName::get($data['role'])->value())
                ->firstOrFail();

            $user->role_id = $role->id;
            $user->save();

            $user = User::getSearchQuery()
                ->where('id', $user->id)
                ->first();

            return response()->json($this->transform($user));
        }

        $this->authorize(UserPolicy::UPDATE, $user);

        if (isset($data['email'])) {
            $data['email_verified_at'] = null;
            $data['token'] = Str::random(72);
        }

        if (isset($data['new_password'])) {
            if (
                ! isset($data['password'])
                || Hash::check($data['password'] ?? null, $user->password)
            ) {
                    throw new HttpException(
                        Response::HTTP_NOT_FOUND,
                        'Invalid current password'
                    );
            }

            $data['password'] = bcrypt($data['new_password']);
        }

        $user->update($data);

        if (isset($data['email'])) {
            $notification = new EmailVerification($user);
            $user->notify($notification);
        }

        return response()->json($this->transform($user));
    }

    /**
     * @param Authenticatable&User $user
     * @throws AuthorizationException
     */
    public function destroy(User $user): Response
    {
        $this->authorize(UserPolicy::DELETE, $user);

        if (! $user->hasRole(RoleName::Customer)) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                sprintf(
                    'Unable to delete profile with %s role',
                    $user->role->name
                )
            );
        }

        $user->delete();

        return response()->noContent();
    }
}

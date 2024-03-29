<?php

namespace App\Http\Requests\User;

use App\Models\User\User;
use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\ValidationFields\IncludeRules;

abstract class BaseUserRequest extends BaseFormRequest
{
    protected function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return array_merge(
            IncludeRules::getRules(User::class),
            [
                'first_name' => [$required, 'string'],
                'last_name' => [$required, 'string'],
                'email' => [$required, 'email', 'unique:users,email'],
                'password' => [$required, 'required_with:new_password', 'min:9'],
                'new_password' => ['sometimes', 'min:9'],
                'role' => ['sometimes', 'string'],
            ]
        );
    }
}

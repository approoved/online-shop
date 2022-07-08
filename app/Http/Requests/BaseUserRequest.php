<?php

namespace App\Http\Requests;

abstract class BaseUserRequest extends BaseFormRequest
{
    protected function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return [
            'first_name' => [
                $required,
                'string',
            ],
            'last_name' => [
                $required,
                'string',
            ],
            'email' => [
                $required,
                'email',
                'unique:users,email',
            ],
            'password' => [
                $required,
                'min:9',
            ],
            'new_password' => [
                'sometimes',
                'min:9',
            ],
            'role' => [
              'sometimes',
              'string',
            ],
        ];
    }
}

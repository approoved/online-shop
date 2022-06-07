<?php

namespace App\Http\Requests;

abstract class BaseUserRequest extends BaseFormRequest
{
    public function getRules(bool $isCreate): array
    {
        $required = $isCreate ? 'required' : 'sometimes';

        return [
            'first_name' => [
                $required,
            ],
            'last_name' => [
                $required,
            ],
            'email' => [
                $required,
                'email',
                'unique:users,email'
            ],
            'password' => [
                $required,
                'min:9'
            ],
        ];
    }
}

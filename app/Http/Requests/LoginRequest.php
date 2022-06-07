<?php

namespace App\Http\Requests;

final class LoginRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
              'required',
            ],
        ];
    }
}

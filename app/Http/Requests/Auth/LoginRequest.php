<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

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

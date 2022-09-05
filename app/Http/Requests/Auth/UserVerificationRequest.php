<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

final class UserVerificationRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'token' => [
                'required',
                'string',
            ],
        ];
    }
}

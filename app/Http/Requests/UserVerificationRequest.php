<?php

namespace App\Http\Requests;

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

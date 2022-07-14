<?php

namespace App\Http\Requests\User;

final class CreateUserRequest extends BaseUserRequest
{
    public function rules(): array
    {
        return $this->getRules(true);
    }
}

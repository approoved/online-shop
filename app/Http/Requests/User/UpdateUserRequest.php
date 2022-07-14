<?php

namespace App\Http\Requests\User;

final class UpdateUserRequest extends BaseUserRequest
{
    public function rules(): array
    {
        return $this->getRules(false);
    }
}

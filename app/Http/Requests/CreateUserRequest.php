<?php

namespace App\Http\Requests;

final class CreateUserRequest extends BaseUserRequest
{
    public function rules(): array
    {
        return $this->getRules(true);
    }
}

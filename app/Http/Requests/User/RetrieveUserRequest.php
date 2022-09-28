<?php

namespace App\Http\Requests\User;

use App\Models\User\User;
use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\ValidationFields\IncludeRules;

class RetrieveUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return IncludeRules::getRules(User::class);
    }
}

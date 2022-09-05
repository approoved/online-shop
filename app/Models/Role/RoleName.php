<?php

namespace App\Models\Role;

use App\Services\Enum\BaseEnum;

enum RoleName
{
    use BaseEnum;

    case customer;
    case manager;
    case admin;
}

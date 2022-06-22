<?php

namespace App\Models\Role;

use App\Src\Enum\BaseEnum;

enum RoleName
{
    use BaseEnum;

    case customer;
    case manager;
    case admin;
}

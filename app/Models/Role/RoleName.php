<?php

namespace App\Models\Role;

use App\Services\Enum\BaseEnum;

enum RoleName
{
    use BaseEnum;

    case Customer;
    case Manager;
    case Admin;
}

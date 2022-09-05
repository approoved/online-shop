<?php

namespace App\Models\ProductField;

use App\Services\Enum\BaseEnum;

enum FieldTypeName
{
    use BaseEnum;

    case text;
    case integer;
    case float;
    case date;
}

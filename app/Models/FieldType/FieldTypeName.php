<?php

namespace App\Models\FieldType;

use App\Services\Enum\BaseEnum;

enum FieldTypeName: string
{
    use BaseEnum;

    case Text = 'text';
    case Integer = 'integer';
    case Float = 'float';
    case Date = 'date';
}

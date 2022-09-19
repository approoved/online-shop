<?php

namespace App\Models\ProductFilterType;

use App\Services\Enum\BaseEnum;

enum ProductFilterTypeName
{
    use BaseEnum;

    case Runtime;
    case Range;
    case Exact;
}

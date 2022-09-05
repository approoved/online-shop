<?php

namespace App\Models\ProductFilter;

use App\Services\Enum\BaseEnum;

enum ProductFilterTypeName
{
    use BaseEnum;

    case Runtime;
    case Range;
    case Exact;
}

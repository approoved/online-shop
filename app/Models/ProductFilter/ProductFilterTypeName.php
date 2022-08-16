<?php

namespace App\Models\ProductFilter;

use App\Src\Enum\BaseEnum;

enum ProductFilterTypeName
{
    use BaseEnum;

    case Runtime;
    case Range;
    case Exact;
}

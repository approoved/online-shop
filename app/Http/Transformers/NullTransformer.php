<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

final class NullTransformer extends TransformerAbstract
{
    /**
     * @param null $null
     */
    public function transform($null): array
    {
        return [];
    }
}

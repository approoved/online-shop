<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class NullTransformer extends TransformerAbstract
{
    /**
     * @param null $null
     * @return array
     */
    public function transform($null): array
    {
        return [];
    }
}

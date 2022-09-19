<?php

namespace App\Services\Fractal;

use League\Fractal\Serializer\ArraySerializer as Serializer;

class ArraySerializer extends Serializer
{
    /**
     * {@inheritDoc}
     */
    public function collection(?string $resourceKey, array $data): array
    {
        return $resourceKey ? [$resourceKey => $data] : $data;
    }
}

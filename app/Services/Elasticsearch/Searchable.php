<?php

namespace App\Services\Elasticsearch;

interface Searchable
{
    public function toSearchArray(): array;

    public static function bootSearchableTrait(): void;
}

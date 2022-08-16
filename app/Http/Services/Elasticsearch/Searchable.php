<?php

namespace App\Http\Services\Elasticsearch;

interface Searchable
{
    public function toSearchArray(): array;

    public static function bootSearchableTrait(): void;
}

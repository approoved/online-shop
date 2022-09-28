<?php

namespace App\Services\Elasticsearch;

interface Searchable
{
    public function searchRepositoryClass(): string;

    public static function bootSearchableTrait(): void;
}

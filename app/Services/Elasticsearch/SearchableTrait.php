<?php

namespace App\Services\Elasticsearch;

trait SearchableTrait
{
    public static function bootSearchableTrait(): void
    {
        static::observe(ElasticsearchObserver::class);
    }
}

<?php

namespace App\Http\Services\Elasticsearch;

use App\Observers\ElasticsearchObserver;

trait SearchableTrait
{
    public static function bootSearchableTrait(): void
    {
        static::observe(ElasticsearchObserver::class);
    }
}

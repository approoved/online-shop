<?php

namespace App\Services\Elasticsearch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Elasticsearch\Repositories\SearchRepository;
use App\Services\Elasticsearch\Repositories\RepositoryFactory;

class ElasticsearchObserver implements ShouldQueue
{
    public function saved(Model&Searchable $model): void
    {
        $this->getRepository($model)->store($model);
    }

    public function deleted(Model&Searchable $model): void
    {
        $this->getRepository($model)->delete($model);
    }

    private function getRepository(Model&Searchable $model): SearchRepository
    {
        return RepositoryFactory::for($model);
    }
}

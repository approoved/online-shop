<?php

namespace App\Services\Elasticsearch\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Services\Elasticsearch\Searchable;

class RepositoryFactory
{
    public static function for(Searchable&Model $model): SearchRepository
    {
        return resolve($model->searchRepositoryClass());
    }
}

<?php

namespace App\Services\Elasticsearch\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Services\Elasticsearch\Searchable;

interface SearchRepository
{
    public function store(Searchable&Model $model): void;

    public function delete(Searchable&Model $model): void;

    public function getIndex(): string;

    public function getId(Searchable&Model $model): int;

    public function toSearchArray(Searchable&Model $model): array;
}

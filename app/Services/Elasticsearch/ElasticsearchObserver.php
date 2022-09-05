<?php

namespace App\Services\Elasticsearch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Queue\ShouldQueue;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;

class ElasticsearchObserver implements ShouldQueue
{
    private Elasticsearch $elasticsearch;

    public function __construct()
    {
        $this->elasticsearch = Elasticsearch::getInstance();
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function created(Model&Searchable $model): void
    {
        $this->elasticsearch->index($model);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function updated(Model&Searchable $model): void
    {
        $this->elasticsearch->index($model);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function deleted(Model&Searchable $model): void
    {
        $this->elasticsearch->delete($model);
    }
}

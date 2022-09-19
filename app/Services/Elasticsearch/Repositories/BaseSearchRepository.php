<?php

namespace App\Services\Elasticsearch\Repositories;

use Elastic\Elasticsearch\Client;
use App\Models\FieldType\FieldTypeName;
use Illuminate\Database\Eloquent\Model;
use Elastic\Elasticsearch\ClientBuilder;
use App\Services\Elasticsearch\Searchable;
use http\Exception\InvalidArgumentException;
use App\Exceptions\InvalidAppConfigurationException;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;

abstract class BaseSearchRepository implements SearchRepository
{
    protected readonly Client $elasticsearch;

    protected const MAX_RESULT_SIZE = 10000;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $config = config('elasticsearch.client');

        $this->elasticsearch = ClientBuilder::create()
            ->setHosts([$config['hosts']])
            ->setBasicAuthentication(
                $config['username'],
                $config['password']
            )
            ->setCABundle($config['ca_cert'])
            ->build();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function checkIndexExists(): bool
    {
        if (
            $this
                ->elasticsearch
                ->indices()
                ->exists(['index' => $this->getIndex()])
                ->getStatusCode() === 404
        ) {
            return false;
        }

        return true;
    }

    /**
     * @throws ClientResponseException
     * @throws InvalidAppConfigurationException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function createIndex(): void
    {
        $config = config('elasticsearch.indices_settings');
        $index = $this->getIndex();

        $this->elasticsearch->indices()->create(['index' => $index]);

        if (isset($config[$index]['mappings'])) {
            $this->putMappings($config[$index]['mappings']);
        }
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function deleteIndex(): void
    {
        $this->elasticsearch->indices()->delete(['index' => $this->getIndex()]);
    }

    /**
     * @throws InvalidAppConfigurationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function putMappings(array $mappings): void
    {
        if (! $this->checkIndexExists()) {
            $this->createIndex();
        }

        foreach ($mappings as $mapping) {
            if (! isset($mapping['field'])) {
                throw new InvalidArgumentException(
                    'The \'field\' field is required.'
                );
            }

            if (! isset($mapping['type'])) {
                throw new InvalidArgumentException(
                    'The \'type\' field is required.'
                );
            }

            if (! $mapping['type'] instanceof FieldTypeName) {
                throw new InvalidArgumentException(
                    'The \'type\' field must be instance of FieldTypeName.'
                );
            }
        }

        $properties = [];
        foreach ($mappings as $mapping) {
            $properties[$mapping['field']] = [
                'type' => 'keyword',
                'fields' => [
                    $mapping['type']->value() => [
                        'type' => $mapping['type']->value(),
                    ],
                ],
            ];
        }

        $this->elasticsearch->indices()->putMapping([
            'index' => $this->getIndex(),
            'body' => [
                'properties' => $properties,
            ],
        ]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function getMappings(): array
    {
        return $this
            ->elasticsearch
            ->indices()
            ->getMapping(['index' => $this->getIndex()])
            ->asArray();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws InvalidAppConfigurationException
     */
    public function store(Searchable&Model $model): void
    {
        if (! $this->checkIndexExists()) {
            $this->createIndex();
        }

        $this->elasticsearch->index([
            'index' => static::getIndex(),
            'id' => $this->getId($model),
            'body' => $this->toSearchArray($model),
        ]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function delete(Searchable&Model $model): void
    {
        $this->elasticsearch->delete([
            'index' => $this->getIndex(),
            'id' => $this->getId($model),
        ]);
    }

    public function getId(Searchable&Model $model): int
    {
        return $model->getKey();
    }

    public function toSearchArray(Searchable&Model $model): array
    {
        return $model->toArray();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function searchOnElasticsearch(array $query = null, array $filters = null): array
    {
        $data = [
            'index' => $this->getIndex(),
            'body' => [
                'size' => static::MAX_RESULT_SIZE,
            ],
        ];

        if ($query) {
            $data['body']['query']['bool']['must'] = $query;
        }

        if ($filters) {
            $data['body']['query']['bool']['filter'] = $filters;
        }

        return $this->elasticsearch->search($data)->asArray();
    }

    abstract public function getIndex(): string;
}

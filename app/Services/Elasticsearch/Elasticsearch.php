<?php

namespace App\Services\Elasticsearch;

use Illuminate\Support\Arr;
use App\Models\Category\Category;
use Elastic\Elasticsearch\Client;
use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Elastic\Elasticsearch\ClientBuilder;
use App\Models\ProductField\FieldTypeName;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ProductFilter\ProductFilterType;
use App\Models\ProductFilter\ProductFilterTypeName;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;

class Elasticsearch
{
    private Client $client;
    private static ?Elasticsearch $instance = null;

    public const MAX_RESULT_SIZE = 10000;

    /**
     * @throws AuthenticationException
     */
    private function __construct()
    {
        $config = config('elasticsearch.client');

        $this->client = ClientBuilder::create()
            ->setHosts([$config['hosts']])
            ->setBasicAuthentication(
                $config['username'],
                $config['password']
            )
            ->setCABundle($config['ca_cert'])
            ->build();
    }

    public static function getInstance(): Elasticsearch
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function createIndex(string $index): void
    {
        $config = config('elasticsearch.indices_settings');

        if (! array_key_exists($index, $config)) {
            throw new RuntimeException(
                sprintf(
                    'Index settings not configurated for %s.',
                    $index
                )
            );
        }

        $properties = [];

        foreach ($config[$index]['mappings'] as $mapping) {
            $properties[$mapping['name']] = [
                'type' => 'keyword',
                'fields' => [
                    $mapping['type']->value() => [
                        'type' => $mapping['type']->value()
                    ]
                ]
            ];
        }

        $this->client->indices()->create([
            'index' => $index,
            'body' => [
                'mappings' => [
                    'properties' => $properties
                ]
            ]
        ]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function putMapping(string $index, string $field, FieldTypeName $typeName): void
    {
        if (
            $this
                ->client
                ->indices()
                ->exists(['index' => $index])
                ->getStatusCode() === 404
        ) {
            $this->createIndex($index);
        }

        $this->client->indices()->putMapping([
            'index' => $index,
            'body' => [
                'properties' => [
                    $field => [
                        'type' => 'keyword',
                        'fields' => [
                            $typeName->value() => [
                                'type' => $typeName->value()
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function index(Model&Searchable $model): void
    {
        $this->client->index([
            'index' => $model->getTable(),
            'id' => $model->getKey(),
            'body' => $model->toSearchArray(),
        ]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function delete(Model&Searchable $model): void
    {
        $this->client->delete([
            'index' => $model->getTable(),
            'id' => $model->getKey(),
        ]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(
        string $index,
        string $query = null,
        array $filters = null,
    ): array {

        $data = [
            'index' => $index,
            'body' => [
                'stored_fields' => [],
                'size' => self::MAX_RESULT_SIZE,
            ]
        ];

        if ($query) {
            $data['body']['query']['bool']['must'] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['*'],
                    'fuzziness' => 'AUTO',
                    'operator' => 'or',
                    'type' => 'most_fields'
                ],
            ];
        }

        if ($filters) {
            $data['body']['query']['bool']['filter'] = $filters;
        }

        $response =  $this->client->search($data);

        return Arr::pluck($response['hits']['hits'], '_id');
    }

    public function getSql(string $index, int $id): array
    {
        $response =  $this->client->sql()->query([
            'body' => [
                'query' => sprintf(
                    'SELECT * FROM %s WHERE id=%s',
                    $index,
                    $id
                )
            ]
        ]);

        return $response->asArray();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function getColumns(string $index, array $filters = null): array
    {
        $query = 'SELECT * FROM ' . $index;

        if ($filters) {
            $filtersCount = 0;

            foreach ($filters as $key => $value) {
                $value = is_array($value) ? implode(',', $value) : $value;

                if ($filtersCount === 0) {
                    $query .= ' WHERE ' . $key . ' IN (' . $value . ')';
                } else {
                    $query .= ' AND ' . $key . ' IN (' . $value . ')';
                }

                $filtersCount++;
            }
        }

        $shouldUseCursor = false;
        do {
            if (! $shouldUseCursor) {
                $response =  $this->client->sql()->query([
                    'body' => [
                        'query' => $query,
                        'fetch_size' => 1000
                    ]
                ]);

                $result = $response->asArray();
                unset($result['cursor']);

                foreach ($result['columns'] as $key => $column) {
                    if ($column['type'] === 'text') {
                        $result['columns'][$key]['name'] .= '.keyword';
                    }
                }
            } else {
                $response = $this->client->sql()->query([
                    'body' => [
                        'cursor' => $response['cursor'],
                        'fetch_size' => 1000
                    ]
                ]);

                $result['rows'] = array_merge($result['rows'], $response['rows']);
            }

            $shouldUseCursor = true;
        } while (isset($response['cursor']));

        return $result;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function getFields(string $index, array $filters = null): array
    {
        $structure = $this->getColumns($index, $filters);

        $array = [];

        foreach ($structure['rows'] as $raw) {
            foreach ($raw as $key => $value) {
                $array[$structure['columns'][$key]['name']][] = $value;
            }
        }

        foreach ($array as $field => $values) {
            foreach ($values as $key => $value) {
                if ($value !== null) {
                    continue 2;
                }
            }
            unset ($array[$field]);
        }

        return array_keys($array);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function getFieldFilterTypeList(Category $category, string $field): Collection
    {
        $config = config('filter-type');

        $columns = $this->getColumns('products', ['category_id' => $category->id])['columns'];

        $fieldType = array_column($columns, 'type', 'name')[$field];

        $types =  $config[$fieldType] ?? $config['default'];

        $typeNames = [];

        foreach ($types as $type) {
            $typeNames[] = ProductFilterTypeName::get($type)->value();
        }

        return ProductFilterType::query()->whereIn('name', $typeNames)->get();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function getAggregatedFilterValues(Category $category, array $aggregations): array
    {
        $filters = [];

        $response = $this->client->search([
            'index' => 'products',
            'size' => 0,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'term' => [
                                'category_id' => $category->id,
                            ]
                        ]
                    ]
                ],
                "aggs" => $aggregations,
            ]
        ]);

        foreach ($response['aggregations'] as $key => $aggregation) {
            $values = [];

            foreach ($aggregation['buckets'] as $k => $bucket) {
                $values[] = [
                    'value' => $bucket['key_as_string'] ?? $bucket['key'] ?? $k,
                    'count' => $bucket['doc_count'],
                ];
            }

            $filters[$key] = $values;
        }

        return $filters;
    }
}

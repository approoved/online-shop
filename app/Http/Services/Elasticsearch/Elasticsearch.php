<?php

namespace App\Http\Services\Elasticsearch;

use Exception;
use App\Models\Category\Category;
use Elastic\Elasticsearch\Client;
use Illuminate\Database\Eloquent\Model;
use Elastic\Elasticsearch\ClientBuilder;
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

    public const MAX_PER_PAGE = 50;
    public const DEFAULT_PER_PAGE = 20;
    public const DEFAULT_RESULT_FROM = 0;

    /**
     * @throws AuthenticationException
     */
    private function __construct()
    {
        $config = config('elasticsearch');

        $this->client = ClientBuilder::create()
            ->setHosts([$config['hosts']])
            ->setBasicAuthentication(
                $config['username'],
                $config['password']
            )
            ->setCABundle($config['ca_cert'])
            ->build();
    }

    private function __clone(): void
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup(): void
    {
        throw new Exception('Cannot unserialize singleton.');
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
        int|null $perPage = self::DEFAULT_PER_PAGE,
        int|null $page = self::DEFAULT_RESULT_FROM
    ): array {

        $data = [
            'index' => $index,
            'body' => [
                'size' => $perPage = $perPage
                    ? min($perPage, self::MAX_PER_PAGE)
                    : self::DEFAULT_PER_PAGE,
                'from' => (($page - 1) * $perPage) ?? self::DEFAULT_RESULT_FROM,
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

        dd($data);

        $response =  $this->client->search($data);

        $result = [];

        foreach ($response['hits']['hits'] as $model) {
            $result[] = $model['_source'];
        }

        return [
            'total' => $response['hits']['total']['value'],
            'data' => $result,
        ];
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function getColumns(Category $category): array
    {
        $response =  $this->client->sql()->query([
            'body' => [
                'query' =>
                    'SELECT * FROM products WHERE category_id=' . $category->id . ' LIMIT 0'
            ]
        ]);

        return $response->asArray();

        $columns = $response['columns'];

        foreach ($columns as $key => $column) {
            if ($column['type'] === 'text') {
                $columns[$key]['name'] .= '.keyword';
            }
        }

        return $columns;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function getFields(Category $category): array
    {
        $columns = $this->getColumns($category);

        $filterFields = array_column($columns, 'name');

        return array_values($filterFields);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function getFieldFilterTypeList(Category $category, string $field): Collection
    {
        $config = config('filter-type');

        $columns = $this->getColumns($category);

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

<?php

namespace App\Services\Elasticsearch\Repositories\Product;

use Illuminate\Support\Arr;
use App\Models\Product\Product;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductField\ProductField;
use App\Services\Elasticsearch\Searchable;
use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidInputDataException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\InvalidAppConfigurationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use App\Services\Elasticsearch\Repositories\BaseSearchRepository;
use App\Services\Elasticsearch\Repositories\Product\Serializers\FilterRequestSerializer;

final class ProductSearchRepository extends BaseSearchRepository
{
    public const ELASTIC_INDEX = 'products';

    public function __construct(private readonly FilterRequestSerializer $serializer)
    {
        parent::__construct();
    }

    /**
     * @throws InvalidAppConfigurationException
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws ResourceNotFoundException
     * @throws InvalidInputDataException
     */
    public function getProductIds(array $data): array
    {
        $filters = [];

        if (isset($data['category_id'])) {
            /** @var Category $category */
            $category = Category::query()->find($data['category_id']);

            if ($category->hasDescendants()) {
                if (isset($data['filter'])) {
                    throw new InvalidInputDataException(
                        'Unable to apply filters to parent category.'
                    );
                }

                $descendantsWithSelf = $category->descendantsWithSelf()->get();

                /** @var Category $category */
                foreach ($descendantsWithSelf as $category) {
                    $categoriesIds[] = $category->id;
                }
            }

            $filters[] = ['terms' => ['category_id' => $categoriesIds ?? [$category->id]]];

            if (isset($data['filter'])) {
                foreach ($data['filter'] as $filterId => $query) {
                    $data['filter'][$filterId] = explode(',', $query);
                }

                foreach ($data['filter'] as $filterId => $query) {
                    /** @var ProductFilter $filter */
                    $filter = $category->filters()->find($filterId);

                    if (! $filter) {
                        throw new ResourceNotFoundException(
                            sprintf(
                                'Filter with id \'%s\' not found in category \'%s\'',
                                $filterId,
                                $category->name
                            )
                        );
                    }

                    $filters[] = $this->serializer->serialize($filter, $query);
                }
            }
        }

        if (isset($data['query'])) {
            $searchQuery = [
                'multi_match' => [
                    'query' => $data['query'],
                    'fields' => ['*'],
                    'fuzziness' => 'AUTO',
                    'operator' => 'or',
                    'type' => 'most_fields',
                ],
            ];
        }

        $response = $this->searchOnElasticsearch(
            $searchQuery ?? null,
            $filters ?? null
        );

        return Arr::pluck($response['hits']['hits'], '_id');
    }

    /**
     * @param Product $model
     */
    public function toSearchArray(Model&Searchable $model): array
    {
        $model->append('short_details');

        return $model->toArray();
    }

    public function getIndex(): string
    {
        return self::ELASTIC_INDEX;
    }

    public function getSearchField(ProductField $field): string
    {
        return sprintf('short_details.%s.%s', $field->group->name, $field->name);
    }
}

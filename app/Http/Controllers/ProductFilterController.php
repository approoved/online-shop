<?php

namespace App\Http\Controllers;

use App\Models\Category\Category;
use App\Policies\ProductFilterPolicy;
use App\Models\ProductFilter\ProductFilter;
use App\Services\Elasticsearch\Elasticsearch;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\ProductFilter\ProductFilterTypeName;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use App\Http\Requests\ProductFilter\CreateProductFilterRequest;
use App\Http\Requests\ProductFilter\UpdateProductFilterRequest;
use App\Http\Requests\ProductFilter\RetrieveProductFilterRequest;

final class ProductFilterController extends Controller
{
    /**
     * @throws AuthorizationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function store(CreateProductFilterRequest $request, Category $category): JsonResponse
    {
        $this->authorize(ProductFilterPolicy::CREATE, ProductFilter::class);

        $data = $request->validated();
        $data['category_id'] = $category->id;

        $productFilter = new ProductFilter();
        $productFilter->store($data);

        return response()->json($productFilter, Response::HTTP_CREATED);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function index(RetrieveProductFilterRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        if ($category->hasDescendants()) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Unable to retrieve filters for parent category.'
            );
        }

        if (isset($data['include'])) {
            if ($data['include'] === 'values') {
                return response()->json($category->filters->load('values'));
            }

            if ($data['include'] === 'aggregated-values') {
                $aggregations = [];

                $filters = $category->filters->load('type', 'values');

                $filters = $filters->reject(function (ProductFilter $value) {
                    return $value->hasType(ProductFilterTypeName::Range, ProductFilterTypeName::Exact)
                        && $value->values()->count() === 0;
                });

                /** @var ProductFilter $filter */
                foreach ($filters as $filter) {
                    $aggregations[$filter->field] = $filter->aggregate();
                }

                $aggregated = Elasticsearch::getInstance()
                    ->getAggregatedFilterValues($category, $aggregations);

                /** @var ProductFilter $filter */
                foreach ($filters as $filter) {
                    $filter->unsetRelations();
                    $filter->values = $aggregated[$filter->field];
                }

                return response()->json($filters);
            }
        }

        return response()->json($category->filters);
    }

    public function show(RetrieveProductFilterRequest $request, ProductFilter $filter): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['include']) && $data['include'] === 'values') {
            return response()->json($filter->load('values'));
        }

        return response()->json($filter);
    }

    /**
     * @throws AuthorizationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function update(UpdateProductFilterRequest $request, ProductFilter $filter): JsonResponse
    {
        $this->authorize(ProductFilterPolicy::UPDATE, $filter);

        $data = $request->validated();

        $filter->store($data);

        return response()->json($filter);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(ProductFilter $filter): Response
    {
        $this->authorize(ProductFilterPolicy::DELETE, $filter);

        $filter->delete();

        return response()->noContent();
    }
}

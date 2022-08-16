<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
use App\Policies\ProductPolicy;
use App\Models\Category\Category;
use App\Models\ProductFilter\ProductFilter;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Services\Elasticsearch\Elasticsearch;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\RetrieveProductRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class ProductController extends Controller
{
    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function index(RetrieveProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $filters = [];

        if (isset($data['category_id'])) {
            /** @var Category $category */
            $category = Category::query()->find($data['category_id']);

            if ($category->hasDescendants()) {
                if (isset($data['filter'])) {
                    throw new HttpException(
                        Response::HTTP_CONFLICT,
                        'Unable to apply filters to parent category.'
                    );
                }

                $descendantsWithSelf = $category->descendantsWithSelf()->get();

                /** @var Category $descendant */
                foreach ($descendantsWithSelf as $descendant) {
                    $categoriesIds[] = $descendant->id;
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
                        throw new HttpException(
                            Response::HTTP_CONFLICT,
                            'Filter with id ' . $filterId . ' not found in category ' . $category->name
                        );
                    }

                    $filters[] = $filter->apply($query);
                }
            }
        }

        return response()->json(Elasticsearch::getInstance()->search(
            index: Product::ELASTIC_INDEX,
            query: $data['query'] ?? null,
            filters: $filters ?? null,
            perPage: $data['per_page'] ?? null,
            page: $data['page'] ?? null
        ));
    }

    /**
     * @throws AuthorizationException
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        $this->authorize(ProductPolicy::CREATE, Product::class);

        $data = $request->validated();

        /** @var Category $category */
        $category = Category::query()
            ->where('name', '=', $data['category'])
            ->firstOrFail();

        $data['category_id'] = $category->id;

        $product = Product::query()->create($data);

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function show(RetrieveProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['include']) && $data['include'] === 'category') {
            $product->load('category');
        }

        return response()->json($product);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize(ProductPolicy::UPDATE, Product::class);

        $data = $request->validated();

        if (isset($data['category'])) {
            /** @var Category $category */
            $category = Category::query()
                ->where('name', '=', $data['category'])
                ->firstOrFail();

            $data['category_id'] = $category->id;
        }

        if (isset($data['add_quantity'])) {
            if ($data['add_quantity'] < 0) {
                $this->authorize(ProductPolicy::DECREASE_QUANTITY, Product::class);
            }

            $data['quantity'] = $product->quantity + $data['add_quantity'];
        }

        $product->update($data);

        return response()->json($product);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Product $product): Response
    {
        $this->authorize(ProductPolicy::DELETE, Product::class);

        $product->delete();

        return response()->noContent();
    }
}

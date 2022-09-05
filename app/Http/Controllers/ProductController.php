<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
use App\Policies\ProductPolicy;
use App\Models\Category\Category;
use App\Models\ProductFilter\ProductFilter;
use App\Services\Elasticsearch\Elasticsearch;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                            Response::HTTP_NOT_FOUND,
                            sprintf(
                                'Filter with id %s not found in category %s',
                                $filterId,
                                $category->name
                            )
                        );
                    }

                    $filters[] = $filter->apply($query);
                }
            }
        }

        $productsIds = Elasticsearch::getInstance()->search(
            index: Product::ELASTIC_INDEX,
            query: $data['query'] ?? null,
            filters: $filters ?? null,
        );

        $products = Product::getSearchQuery()
            ->whereIn('id', $productsIds)
            ->paginate(perPage: $data['per_page'] ?? null, page: $data['page'] ?? null);

        $products = $this->transform($products, $data['append'] ?? null);

        return response()->json($products);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(CreateProductRequest $request, Category $category): JsonResponse
    {
        $this->authorize(ProductPolicy::CREATE, Product::class);

        $data = $request->validated();

        $data['category_id'] = $category->id;
        $data['quantity'] = 0;

        $product = new Product;
        $product->store($data);

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function show(RetrieveProductRequest $request, int $productId): JsonResponse
    {
        $data = $request->validated();

        /** @var Product $product */
        $product = Product::getSearchQuery()
            ->where('id', $productId)
            ->first();

        $product = $this->transform($product, $data['append'] ?? null);

        return response()->json($product);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize(ProductPolicy::UPDATE, Product::class);

        $data = $request->validated();

        if (isset($data['add_quantity'])) {
            if ($data['add_quantity'] < 0) {
                $this->authorize(ProductPolicy::DECREASE_QUANTITY, Product::class);
            }

            $data['quantity'] = $product->quantity + $data['add_quantity'];
        }

        $product->store($data);

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

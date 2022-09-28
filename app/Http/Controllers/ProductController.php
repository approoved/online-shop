<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
use App\Policies\ProductPolicy;
use App\Models\Category\Category;
use App\Exceptions\InvalidDataTypeException;
use App\Exceptions\InvalidInputDataException;
use App\Exceptions\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Product\GetProductRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Exceptions\InvalidAppConfigurationException;
use App\Http\Requests\Product\GetProductListRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use App\Services\Elasticsearch\Repositories\Product\ProductSearchRepository;

final class ProductController extends Controller
{
    /**
     * @throws InvalidAppConfigurationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function index(
        GetProductListRequest   $request,
        ProductSearchRepository $repository
    ): JsonResponse {
        $data = $request->validated();

        try {
            $productsIds = $repository->getProductIds($data);
        } catch (ResourceNotFoundException $exception) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND,
                $exception->getMessage()
            );
        } catch (InvalidInputDataException $exception) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                $exception->getMessage()
            );
        } catch (InvalidDataTypeException $exception) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $exception->getMessage()
            );
        }

        $products = Product::getSearchQuery()
            ->whereIn('id', $productsIds)
            ->paginate($data['per_page'] ?? null);

        return $this->transformToJson($products, $data['append'] ?? null);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(CreateProductRequest $request, Category $category): JsonResponse
    {
        $this->authorize(ProductPolicy::CREATE, Product::class);

        $data = $request->validated();

        if ($category->hasDescendants()) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Unable to add product to parent category.'
            );
        }

        $data['category_id'] = $category->id;
        $data['quantity'] = 0;

        $product = new Product();

        try {
            $product->store($data);
        } catch (InvalidDataTypeException $exception) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $exception->getMessage()
            );
        }

        return $this->transformToJson($product, status:Response::HTTP_CREATED);
    }

    public function show(GetProductRequest $request, int $productId): JsonResponse
    {
        $data = $request->validated();

        /** @var Product $product */
        $product = Product::getSearchQuery()
            ->where('id', $productId)
            ->firstOrFail();

        return $this->transformToJson($product, $data['append'] ?? null);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize(ProductPolicy::UPDATE, $product);

        $data = $request->validated();

        if (isset($data['add_quantity'])) {
            if ($data['add_quantity'] < 0) {
                $this->authorize(ProductPolicy::DECREASE_QUANTITY, $product);
            }

            $data['quantity'] = $product->quantity + $data['add_quantity'];
        }

        try {
            $product->store($data);
        } catch (InvalidDataTypeException $exception) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $exception->getMessage()
            );
        }

        $product = Product::getSearchQuery()
            ->where('id', $product->id)
            ->first();

        return $this->transformToJson($product);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Product $product): Response
    {
        $this->authorize(ProductPolicy::DELETE, $product);

        $product->delete();

        return response()->noContent();
    }
}

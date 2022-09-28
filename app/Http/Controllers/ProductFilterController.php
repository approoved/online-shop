<?php

namespace App\Http\Controllers;

use App\Models\Category\Category;
use App\Policies\ProductFilterPolicy;
use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidInputDataException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Exceptions\InvalidAppConfigurationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Requests\ProductFilter\CreateProductFilterRequest;
use App\Http\Requests\ProductFilter\UpdateProductFilterRequest;
use App\Http\Requests\ProductFilter\RetrieveProductFilterRequest;
use App\Models\ProductFilter\Exceptions\InvalidFilterTypeException;

final class ProductFilterController extends Controller
{
    /**
     * @throws AuthorizationException
     * @throws InvalidAppConfigurationException
     */
    public function store(CreateProductFilterRequest $request, Category $category): JsonResponse
    {
        $this->authorize(ProductFilterPolicy::CREATE, ProductFilter::class);

        $data = $request->validated();
        $data['category_id'] = $category->id;

        $filter = new ProductFilter();

        try {
            $filter->store($data);
        } catch (InvalidFilterTypeException $exception) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $exception->getMessage()
            );
        } catch (InvalidInputDataException $exception) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                $exception->getMessage()
            );
        }

        return $this->transformToJson($filter, status: Response::HTTP_CREATED);
    }

    public function index(RetrieveProductFilterRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        $filters = ProductFilter::getSearchQuery()
            ->where('category_id', $category->id)
            ->paginate();

        return $this->transformToJson($filters, $data['append'] ?? null);
    }

    public function show(RetrieveProductFilterRequest $request, int $filterId): JsonResponse
    {
        $data = $request->validated();

        /** @var ProductFilter $filter */
        $filter = ProductFilter::getSearchQuery()
            ->where('id', $filterId)
            ->firstOrFail();

        return $this->transformToJson($filter, $data['append'] ?? null);
    }

    /**
     * @throws AuthorizationException
     * @throws InvalidAppConfigurationException
     */
    public function update(UpdateProductFilterRequest $request, ProductFilter $filter): JsonResponse
    {
        $this->authorize(ProductFilterPolicy::UPDATE, $filter);

        $data = $request->validated();

        try {
            $filter->store($data);
        } catch (InvalidFilterTypeException $exception) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $exception->getMessage()
            );
        } catch (InvalidInputDataException $exception) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                $exception->getMessage()
            );
        }

        /** @var ProductFilter $filter */
        $filter = ProductFilter::getSearchQuery()
            ->where('id', $filter->id)
            ->first();

        return $this->transformToJson($filter);
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

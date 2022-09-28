<?php

namespace App\Http\Controllers;

use App\Policies\ProductFilterValuePolicy;
use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidDataTypeException;
use App\Exceptions\InvalidInputDataException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\ProductFilterValue\ProductFilterValue;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\ProductFilter\Exceptions\InvalidFilterTypeException;
use App\Http\Requests\ProductFilterValue\GetProductFilterValueRequest;
use App\Http\Requests\ProductFilterValue\CreateProductFilterValueRequest;
use App\Http\Requests\ProductFilterValue\UpdateProductFilterValueRequest;
use App\Http\Requests\ProductFilterValue\GetProductFilterValueListRequest;

class ProductFilterValueController extends Controller
{
    /**
     * @throws HttpException
     * @throws AuthorizationException
     */
    public function store(
        CreateProductFilterValueRequest $request,
        ProductFilter $filter
    ): JsonResponse {
        $this->authorize(
            ProductFilterValuePolicy::CREATE,
            ProductFilterValue::class
        );

        $data = $request->validated();
        $data['product_filter_id'] = $filter->id;

        $value = new ProductFilterValue();

        try {
            $value->store($data);
        } catch (InvalidFilterTypeException|InvalidDataTypeException $exception) {
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

        return $this->transformToJson($value, status: Response::HTTP_CREATED);
    }

    /**
     * @throws AuthorizationException
     */
    public function index(
        GetProductFilterValueListRequest $request,
        ProductFilter                    $filter
    ): JsonResponse {
        $this->authorize(
            ProductFilterValuePolicy::VIEW_ANY,
            ProductFilterValue::class
        );

        $data = $request->validated();

        $values = ProductFilterValue::getSearchQuery()
            ->where('product_filter_id', $filter->id)
            ->paginate($data['per_page'] ?? null);

        return $this->transformToJson($values);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(GetProductFilterValueRequest $request, int $valueId): JsonResponse
    {
        $data = $request->validated();

        /** @var ProductFilterValue $value */
        $value = ProductFilterValue::getSearchQuery()
            ->where('id', $valueId)
            ->firstOrFail();

        $this->authorize(ProductFilterValuePolicy::VIEW, $value);

        return $this->transformToJson($value);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(
        UpdateProductFilterValueRequest $request,
        ProductFilterValue $value
    ): JsonResponse {
        $this->authorize(ProductFilterValuePolicy::UPDATE, $value);

        $data = $request->validated();

        try {
            $value->store($data);
        } catch (InvalidFilterTypeException|InvalidDataTypeException $exception) {
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

        /** @var ProductFilterValue $value */
        $value = ProductFilterValue::getSearchQuery()
            ->where('id', $value->id)
            ->first();

        return $this->transformToJson($value);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(ProductFilterValue $value): Response
    {
        $this->authorize(ProductFilterValuePolicy::DELETE, $value);

        $value->delete();

        return response()->noContent();
    }
}

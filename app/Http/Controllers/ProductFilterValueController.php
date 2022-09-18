<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Policies\ProductFilterValuePolicy;
use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidDataTypeException;
use App\Exceptions\InvalidInputDataException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\ProductFilterValue\ProductFilterValue;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\ProductFilter\Exceptions\InvalidFilterTypeException;
use App\Http\Requests\ProductFilterValue\CreateProductFilterValueRequest;
use App\Http\Requests\ProductFilterValue\UpdateProductFilterValueRequest;
use App\Http\Requests\ProductFilterValue\RetrieveProductFilterValueRequest;

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

        return response()->json(
            $this->transform($value), Response::HTTP_CREATED
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function index(
        RetrieveProductFilterValueRequest $request,
        ProductFilter $filter
    ): JsonResponse {
        $this->authorize(
            ProductFilterValuePolicy::VIEW_ANY,
            ProductFilterValue::class
        );

        $data = $request->validated();

        $values = ProductFilterValue::getSearchQuery()
            ->where('product_filter_id', $filter->id)
            ->paginate(perPage: $data['per_page'] ?? null, page: $data['page'] ?? null);

        return response()->json($this->transform($values));
    }

    /**
     * @throws AuthorizationException
     */
    public function show(int $valueId): JsonResponse
    {
        /** @var ProductFilterValue $value */
        $value = ProductFilterValue::getSearchQuery()
            ->where('id', $valueId)
            ->firstOrFail();

        $this->authorize(ProductFilterValuePolicy::VIEW, $value);

        return response()->json($this->transform($value));
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

        return response()->json($this->transform($value));
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

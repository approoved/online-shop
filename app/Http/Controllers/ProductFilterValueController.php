<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Policies\ProductFilterValuePolicy;
use App\Models\ProductFilter\ProductFilter;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ProductFilter\ProductFilterValue;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Requests\ProductFilterValue\UpdateProductFilterValueRequest;
use App\Http\Requests\ProductFilterValue\CreateProductFilterValueRequest;

class ProductFilterValueController extends Controller
{
    /**
     * @throws HttpException
     * @throws AuthorizationException
     */
    public function store(CreateProductFilterValueRequest $request, ProductFilter $filter): JsonResponse
    {
        $this->authorize(ProductFilterValuePolicy::CREATE, ProductFilterValue::class);

        $data = $request->validated();

        $data['product_filter_id'] = $filter->id;

        $filterValue = new ProductFilterValue();
        $filterValue->store($data);

        return response()->json($filterValue, Response::HTTP_CREATED);
    }

    /**
     * @throws AuthorizationException
     */
    public function index(ProductFilter $filter): JsonResponse
    {
        $this->authorize(ProductFilterValuePolicy::VIEW_ANY, ProductFilterValue::class);

        return response()->json($filter->values);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(ProductFilterValue $value): JsonResponse
    {
        $this->authorize(ProductFilterValuePolicy::VIEW, ProductFilterValue::class);

        return response()->json($value);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateProductFilterValueRequest $request, ProductFilterValue $value): JsonResponse
    {
        $this->authorize(ProductFilterValuePolicy::UPDATE, $value);

        $data = $request->validated();

        $value->store($data);

        return response()->json($value);
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

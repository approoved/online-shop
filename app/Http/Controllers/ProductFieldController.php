<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Policies\ProductFieldPolicy;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\ProductField\ProductField;
use App\Models\ProductField\ProductFieldGroup;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Requests\ProductField\CreateProductFieldRequest;

final class ProductFieldController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function store(CreateProductFieldRequest $request, ProductFieldGroup $group): JsonResponse
    {
        $this->authorize(
            ProductFieldPolicy::CREATE,
            ProductField::class
        );

        $data = $request->validated();
        $data['product_field_group_id'] = $group->id;

        $field = new ProductField();
        $field->store($data);

        return response()->json($field, Response::HTTP_CREATED);
    }

    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize(
            ProductFieldPolicy::VIEW_ANY,
            ProductField::class
        );

        $fields = ProductField::getSearchQuery()->get();

        return response()->json($this->transform($fields));
    }

    /**
     * @throws AuthorizationException
     */
    public function show(ProductField $fieldId): JsonResponse
    {
        $field = ProductField::getSearchQuery()
            ->where('id', $fieldId)
            ->first();

        $this->authorize(ProductFieldPolicy::VIEW, $field);

        return response()->json($this->transform($field));
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(ProductField $field): Response
    {
        $this->authorize(ProductFieldPolicy::DELETE, $field);

        if ($field->hasProducts()) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Unable to delete product field with products.'
            );
        }

        $field->delete();

        return response()->noContent();
    }
}

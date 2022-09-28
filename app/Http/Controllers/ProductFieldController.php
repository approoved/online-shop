<?php

namespace App\Http\Controllers;

use App\Policies\ProductFieldPolicy;
use App\Models\ProductField\ProductField;
use App\Exceptions\InvalidInputDataException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\ProductFieldGroup\ProductFieldGroup;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Requests\ProductField\CreateProductFieldRequest;
use App\Http\Requests\ProductField\RetrieveProductFieldRequest;

final class ProductFieldController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function store(
        CreateProductFieldRequest $request,
        ProductFieldGroup $group
    ): JsonResponse {
        $this->authorize(
            ProductFieldPolicy::CREATE,
            ProductField::class
        );

        $data = $request->validated();
        $data['product_field_group_id'] = $group->id;

        $field = new ProductField();

        try {
            $field->store($data);
        } catch (InvalidInputDataException $exception) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                $exception->getMessage()
            );
        }

        return $this->transformToJson($field, status: Response::HTTP_CREATED);
    }

    /**
     * @throws AuthorizationException
     */
    public function index(RetrieveProductFieldRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->authorize(
            ProductFieldPolicy::VIEW_ANY,
            ProductField::class
        );

        $fields = ProductField::getSearchQuery()->paginate();

        return $this->transformToJson($fields);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(RetrieveProductFieldRequest $request, int $fieldId): JsonResponse
    {
        $data = $request->validated();

        $field = ProductField::getSearchQuery()
            ->where('id', $fieldId)
            ->firstOrFail();

        $this->authorize(ProductFieldPolicy::VIEW, $field);

        return $this->transformToJson($field);
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
                'Unable to delete product field with products. Delete products first.'
            );
        }

        $field->delete();

        return response()->noContent();
    }
}

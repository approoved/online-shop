<?php

namespace App\Http\Controllers;

use Spatie\QueryBuilder\QueryBuilder;
use App\Policies\ProductFieldGroupPolicy;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ProductField\ProductFieldGroup;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Requests\ProductFieldGroup\CreateProductFieldGroupRequest;

final class ProductFieldGroupController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function store(CreateProductFieldGroupRequest $request): JsonResponse
    {
        $this->authorize(
            ProductFieldGroupPolicy::CREATE,
            ProductFieldGroup::class
        );

        $data = $request->validated();

        $group = new ProductFieldGroup();
        $group->fill($data)->save();

        return response()->json($group, Response::HTTP_CREATED);
    }

    public function index(): JsonResponse
    {
        $groups = ProductFieldGroup::getSearchQuery()->get();

        return response()->json($this->transform($groups));
    }

    public function show(ProductFieldGroup $groupId): JsonResponse
    {
        $group = ProductFieldGroup::getSearchQuery()
            ->where('id', $groupId)
            ->first();

        return response()->json($this->transform($group));
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(ProductFieldGroup $group): Response
    {
        $this->authorize(ProductFieldGroupPolicy::DELETE, $group);

        if ($group->hasProducts()) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Unable to delete product field group with products.'
            );
        }

        $group->delete();

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Policies\ProductFieldGroupPolicy;
use App\Exceptions\InvalidInputDataException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\ProductFieldGroup\ProductFieldGroup;
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

        return $this->transformToJson($group, status: Response::HTTP_CREATED);
    }

    public function index(): JsonResponse
    {
        $groups = ProductFieldGroup::getSearchQuery()->paginate();

        return $this->transformToJson($groups);
    }

    public function show(int $groupId): JsonResponse
    {
        $group = ProductFieldGroup::getSearchQuery()
            ->where('id', $groupId)
            ->firstOrFail();

        return $this->transformToJson($group);
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
                'Unable to delete product field group with products. Delete products first.'
            );
        }

        $group->delete();

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User\User;
use Illuminate\Support\Arr;
use App\Models\Role\RoleName;
use App\Models\Category\Category;
use Illuminate\Support\Facades\Gate;
use App\Models\ProductField\ProductField;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Exceptions\InvalidAppConfigurationException;

final class ProductFilterConfigurationController extends Controller
{
    public function getFieldList(Category $category): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->hasRole(RoleName::Admin, RoleName::Manager));

        $fieldsIds = Arr::pluck($category->fields, 'id');

        $fields = ProductField::getSearchQuery()
            ->whereIn('id', $fieldsIds)
            ->get();

        return $this->transformToJson($fields);
    }

    /**
     * @throws InvalidAppConfigurationException
     */
    public function getFilterTypeList(ProductField $field): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->hasRole(RoleName::Admin, RoleName::Manager));

        $filterTypes = $field->getAvailableFilterTypes();

        return $this->transformToJson($filterTypes);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilter\GetFieldListRequest;
use App\Http\Requests\ProductFilter\GetFilterTypeListRequest;
use App\Http\Services\Elasticsearch\Elasticsearch;
use App\Models\Category\Category;
use App\Models\ProductFilter\ProductFilter;
use App\Models\ProductFilter\ProductFilterType;
use App\Models\Role\RoleName;
use App\Models\User;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductFilterConfigurationController extends Controller
{
    private Elasticsearch $elasticsearch;

    public function __construct()
    {
        $this->elasticsearch = Elasticsearch::getInstance();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function getFieldList(GetFieldListRequest $request, Category $category): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->hasRole(RoleName::admin, RoleName::manager));

        $data = $request->validated();

        $filterFields = $this->elasticsearch->getFields($category);

        //TODO delete exclude

        if (isset($data['exclude']) && $data['exclude'] === 'taken') {
            /** @var ProductFilter $filter */
            foreach ($category->filters as $filter) {
                if ($key = array_search($filter->field, $filterFields)) {
                    unset($filterFields[$key]);
                }
            }
        }

        return response()->json(array_values($filterFields));
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function getFilterTypeList(GetFilterTypeListRequest $request, Category $category): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->hasRole(RoleName::admin, RoleName::manager));

        $data = $request->validated();

        $filterTypes = $this->elasticsearch->getFieldFilterTypeList($category, $data['field']);

        return response()->json($filterTypes);
    }
}

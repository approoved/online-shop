<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role\RoleName;
use App\Models\Product\Product;
use App\Models\Category\Category;
use Illuminate\Support\Facades\Gate;
use App\Models\ProductFilter\ProductFilter;
use App\Services\Elasticsearch\Elasticsearch;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\ProductFilter\GetFieldListRequest;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use App\Http\Requests\ProductFilter\GetFilterTypeListRequest;

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

        $filterFields = $this->elasticsearch->getFields(Product::ELASTIC_INDEX, ['category_id' => $category->id]);

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

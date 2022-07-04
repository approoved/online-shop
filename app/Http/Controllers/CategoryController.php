<?php

namespace App\Http\Controllers;

use App\Policies\CategoryPolicy;
use App\Models\Category\Category;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Category\CategoryRequest;
use Franzose\ClosureTable\Extensions\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\Category\RetrieveCategoryRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CategoryController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function store(CategoryRequest $request, Category $parent = null): JsonResponse
    {
        $this->authorize(CategoryPolicy::STORE, Category::class);

        $data = $request->validated();

        if ($parent) {
            $data['parent_id'] = $parent->id;
        }

        /** @var Category $category */
        $category = Category::query()->create($data);

        return response()->json($category, Response::HTTP_CREATED);
    }

    public function index(RetrieveCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (key_exists('include', $data)) {
            if (strtolower($data['include']) === 'descendants') {
                /** @var Collection $categories */
                $categories = Category::all();

                return response()->json($categories->toTree());
            }
        }

        return response()->json(Category::query()->paginate(10));
    }

    public function show(Category $category, RetrieveCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (key_exists('include', $data)) {
            if (strtolower($data['include']) === 'ancestors') {
                return response()->json($category->appendAncestors());
            }
        }

        return response()->json($category);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize(CategoryPolicy::UPDATE, Category::class);

        $data = $request->validated();

        $category->update($data);

        return response()->json($category);
    }

    /**
     * @throws AuthorizationException
     * @throws HttpException
     */
    public function destroy(Category $category): Response
    {
        $this->authorize(CategoryPolicy::DESTROY, Category::class);

        if ($category->hasDescendants()) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Unable to delete parent category.'
            );
        }

        $category->delete();

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Policies\CategoryPolicy;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Requests\Category\RetrieveCategoryRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Franzose\ClosureTable\Extensions\Collection as ClosureTableCollection;

final class CategoryController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $this->authorize(CategoryPolicy::CREATE, Category::class);

        $data = $request->validated();

        if (isset($data['parent_id'])) {
            $this->validateParentCategory($data['parent_id']);
        }

        /** @var Category $category */
        $category = Category::query()->create($data);

        return $this->transformToJson($category, status: Response::HTTP_CREATED);
    }

    public function index(RetrieveCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($data['serialize'] ?? null === 'tree') {
            /** @var ClosureTableCollection $categories */
            $categories = Category::all();

            return response()->json($categories->toTree());
        }

        /** @var Collection|iterable<int, Category> $categories */
        $categories = Category::getSearchQuery()
            ->paginate();

        return $this->transformToJson($categories);
    }

    public function show(RetrieveCategoryRequest $request, int $categoryId): JsonResponse
    {
        $data = $request->validated();

        /** @var Category $category */
        $category = Category::getSearchQuery()
            ->where('id', $categoryId)
            ->firstOrFail();

        return $this->transformToJson($category, $data['append'] ?? null);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize(CategoryPolicy::UPDATE, $category);

        $data = $request->validated();

        if (isset($data['parent_id'])) {
            $this->validateParentCategory($data['parent_id']);
        }

        $category->update($data);

        $category = Category::getSearchQuery()
            ->where('id', $category->id)
            ->first();

        return $this->transformToJson($category);
    }

    /**
     * @throws AuthorizationException
     * @throws HttpException
     */
    public function destroy(Category $category): Response
    {
        $this->authorize(CategoryPolicy::DELETE, $category);

        if ($category->hasDescendants()) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Unable to delete parent category. Delete children categories first.'
            );
        }

        if ($category->hasProducts()) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Unable to delete category with products. Delete products first.'
            );
        }

        $category->delete();

        return response()->noContent();
    }

    private function validateParentCategory(int $parentId): void
    {
        /** @var Category $parentCategory */
        $parentCategory = Category::query()
            ->where('id', $parentId)
            ->first();

        if ($parentCategory->hasProducts()) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Unable to add child category to category with products.'
            );
        }
    }
}

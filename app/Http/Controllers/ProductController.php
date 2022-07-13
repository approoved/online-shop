<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Policies\ProductPolicy;
use App\Models\Category\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\RetrieveProductRequest;

class ProductController extends Controller
{
    public function index(RetrieveProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['category'])) {
            /** @var Category $category */
            $category = Category::query()
                ->where('name', '=', $data['category'])
                ->firstOrFail();

            if (! $category->hasDescendants()) {
                return response()->json($category->products()->paginate(30));
            }

            $categories = $category->descendantsWithSelf()->get();

            $categoriesIDs = [];

            foreach ($categories as $category) {
                $categoriesIDs[] = $category->id;
            }

            $products = Product::query()
                ->whereIn('category_id', $categoriesIDs)
                ->paginate(30);

            return response()->json($products);
        }

        $products = Product::query()->paginate(30);


        return response()->json($products);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        $this->authorize(ProductPolicy::CREATE, Product::class);

        $data = $request->validated();

        /** @var Category $category */
        $category = Category::query()
            ->where('name', '=', $data['category'])
            ->firstOrFail();

        $data['category_id'] = $category->id;

        $product = Product::query()->create($data);

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function show(RetrieveProductRequest $request,Product $product): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['include'])) {
            if (strtolower($data['include']) === 'category') {
                $product->load('category');
            }
        }

        return response()->json($product);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize(ProductPolicy::UPDATE, Product::class);

        $data = $request->validated();

        if (isset($data['category'])) {
            /** @var Category $category */
            $category = Category::query()
                ->where('name', '=', $data['category'])
                ->firstOrFail();

            $data['category_id'] = $category->id;
        }

        if (isset($data['add_quantity'])) {
            if ($data['add_quantity'] < 0) {
                $this->authorize(ProductPolicy::DECREASE_QUANTITY, Product::class);
            }

            $data['quantity'] = $product->quantity + $data['add_quantity'];
        }

        $product->update($data);

        return response()->json($product);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Product $product): Response
    {
        $this->authorize(ProductPolicy::DELETE, Product::class);

        $product->delete();

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Category\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->categoryService->getAllCategories(),
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->categoryService->getCategoryById($category->id),
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $updatedCategory = $this->categoryService->updateCategory(
            $category,
            $request->validated(),
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully.',
            'data' => $updatedCategory,
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->deleteCategory($category);

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully.',
        ]);
    }
}

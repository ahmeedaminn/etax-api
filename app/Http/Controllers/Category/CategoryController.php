<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Category\CategoryService;
use App\Http\Requests\Category\StoreCategoryRequest;
use PhpParser\Node\Stmt\TryCatch;
use App\Models\Category;

class CategoryController extends Controller
{
    protected $categoryService;

    // We inject the Service here
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    // NEW: Fetch all categories (GET /api/categories)
    public function index()
    {
        try {
            $categories = $this->categoryService->getAllCategories();

            // Eager-load the files so React actually receives them!
            $categories = Category::with('image')->latest()->get();

            return response()->json([
                'status' => 'success',
                'data'   => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch categories.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // NEW: Fetch a single category (GET /api/categories/{category})
    public function show($categoryId)
    {
        try {
            $category = $this->categoryService->getCategoryById($categoryId);

            return response()->json([
                'status' => 'success',
                'data'   => $category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Category not found.',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            // 1. The data is already validated. Just grab it.
            $validatedData = $request->validated();

            // 2. Send the validated data to the Service layer
            $category = $this->categoryService->createCategory($validatedData);


            // 3. Return a clean JSON response
            return response()->json([
                'status'  => 'success',
                'message' => 'Category created successfully.',
                'data'    => $category
            ], 201);
        } catch (\Exception $e) {
            // 4. If ANYTHING fails (Database crash, Service error), catch it here!
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create category.',
                'error'   => $e->getMessage() // Tells you exactly what crashed
            ], 500); // 500 means "Internal Server Error"
        }
    }
}

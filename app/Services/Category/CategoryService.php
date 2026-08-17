<?php

namespace App\Services\Category;
use App\Models\Category; // <--- Add this line right here!
use App\Repositories\Interfaces\Category\CategoryRepositoryInterface;

class CategoryService
{
    protected $categoryRepository;

    // DEPENDENCY INJECTION: We inject the Repository Interface here!
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function createCategory(array $data)
    {
        // Here is where you could add extra logic later (e.g., slugifying the name)
        return $this->categoryRepository->create($data);
    }

    // NEW: Get all categories for the React Dashboard
    public function getAllCategories()
    {
        // @TODO: Refactor to $this->categoryRepository->getAll() later
        return Category::latest()->get(); 
    }

    // NEW: Get a single category by its ID
    public function getCategoryById($id)
    {
        // @TODO: Refactor to $this->categoryRepository->findById($id) later
        return Category::findOrFail($id);
    }
}
<?php

namespace App\Services\Category;

use App\Models\Category;
use App\Repositories\Interfaces\Category\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getCategoryById(int $id): Category
    {
        $category = $this->categoryRepository->findById($id);

        if (! $category) {
            throw (new ModelNotFoundException)->setModel(Category::class, [$id]);
        }

        return $category;
    }

    public function createCategory(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $this->categoryRepository->update($category, $data);

        return $category->refresh();
    }

    public function deleteCategory(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }
}

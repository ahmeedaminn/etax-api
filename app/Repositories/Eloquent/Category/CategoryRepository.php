<?php

namespace App\Repositories\Eloquent\Category;

use App\Models\Category;
use App\Repositories\Interfaces\Category\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): Collection
    {
        return Category::query()
            ->with('image')
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?Category
    {
        return Category::query()
            ->with('image')
            ->find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}

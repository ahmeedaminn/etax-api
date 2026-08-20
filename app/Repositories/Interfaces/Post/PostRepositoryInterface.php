<?php

namespace App\Repositories\Interfaces\Post;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{
    public function all(string $sort = 'latest'): Collection;

    public function findById(int $id): ?Post;

    public function forCategory(int $categoryId): Collection;

    public function forInstitution(int $institutionId): Collection;

    public function create(array $data): Post;

    public function update(Post $post, array $data): bool;

    public function delete(Post $post): bool;
}

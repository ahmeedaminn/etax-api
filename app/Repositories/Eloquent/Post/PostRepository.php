<?php

namespace App\Repositories\Eloquent\Post;

use App\Models\Post;
use App\Repositories\Interfaces\Post\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function findById(int $id): ?Post
    {
        return Post::query()
            ->with(['institution.institutionProfile', 'category', 'files'])
            ->find($id);
    }

    public function forCategory(int $categoryId): Collection
    {
        return Post::query()
            ->with(['institution.institutionProfile', 'files'])
            ->where('category_id', $categoryId)
            ->latest()
            ->get();
    }

    public function forInstitution(int $institutionId): Collection
    {
        return Post::query()
            ->with(['category', 'files'])
            ->where('institution_id', $institutionId)
            ->latest()
            ->get();
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data): bool
    {
        return $post->update($data);
    }

    public function delete(Post $post): bool
    {
        return (bool) $post->delete();
    }
}

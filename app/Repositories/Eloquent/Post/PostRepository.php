<?php

namespace App\Repositories\Eloquent\Post;

use App\Enums\ParticipationStatus;
use App\Models\Post;
use App\Repositories\Interfaces\Post\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function all(string $sort = 'latest'): Collection
    {
        $query = $this->queryWithStatistics();

        if ($sort === 'random') {
            $query->inRandomOrder();
        } else {
            $query->latest();
        }

        return $query->get();
    }

    public function findById(int $id): ?Post
    {
        return $this->queryWithStatistics()->find($id);
    }

    public function forCategory(int $categoryId): Collection
    {
        return $this->queryWithStatistics()
            ->where('category_id', $categoryId)
            ->latest()
            ->get();
    }

    public function forInstitution(int $institutionId): Collection
    {
        return $this->queryWithStatistics()
            ->where('institution_id', $institutionId)
            ->latest()
            ->get();
    }

    public function create(array $data): Post
    {
        $post = Post::create($data);

        return $this->findById($post->id) ?? $post;
    }

    public function update(Post $post, array $data): bool
    {
        return $post->update($data);
    }

    public function delete(Post $post): bool
    {
        return (bool) $post->delete();
    }

    private function queryWithStatistics(): Builder
    {
        return Post::query()
            ->with(['institution.institutionProfile', 'category', 'files'])
            ->withCount([
                'eventParticipations as interested_count' => fn (Builder $query) => $query
                    ->where('status', ParticipationStatus::INTERESTED->value),
                'eventParticipations as going_count' => fn (Builder $query) => $query
                    ->where('status', ParticipationStatus::GOING->value),
                'savedByUsers as saved_count',
            ]);
    }
}

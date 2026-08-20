<?php

namespace App\Services\Post;

use App\Enums\PostType;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Repositories\Interfaces\Post\PostRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class PostService
{
    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function createPost(
        User $institution,
        Category $category,
        array $data
    ): Post {
        $data['institution_id'] = $institution->id;
        $data['category_id'] = $category->id;

        return $this->postRepository->create($data);
    }

    public function getPostById(int $postId): ?Post
    {
        // fetch post by its id
        return $this->postRepository->findById($postId);
    }

    public function getAllPosts(string $sort = 'latest'): Collection
    {
        return $this->postRepository->all($sort);
    }

    public function getPostsByCategory(int $categoryId): Collection
    {
        // Fetch posts for this category, and eager-load the user and files!
        return $this->postRepository->forCategory($categoryId);
    }

    public function getPostsByInstitution(int $institutionId): Collection
    {
        return $this->postRepository->forInstitution($institutionId);
    }

    public function updatePost(Post $post, array $data): Post
    {
        $eventFields = ['start_at', 'end_at', 'location', 'capacity'];

        if ($post->type === PostType::ANNOUNCEMENT
            && array_intersect($eventFields, array_keys($data))) {
            throw ValidationException::withMessages([
                'type' => ['Announcements cannot contain Event fields.'],
            ]);
        }

        if ($post->type === PostType::EVENT) {
            // PATCH may send one date, so compare the final new/existing pair here.
            $startAt = array_key_exists('start_at', $data)
                ? Carbon::parse($data['start_at'])
                : $post->start_at;
            $endAt = array_key_exists('end_at', $data)
                ? Carbon::parse($data['end_at'])
                : $post->end_at;

            if ($startAt && $endAt && $endAt->lessThanOrEqualTo($startAt)) {
                throw ValidationException::withMessages([
                    'end_at' => ['The end date must be after the start date.'],
                ]);
            }
        }

        $this->postRepository->update($post, $data);

        // Return the updated Post with relationships and calculated statistics.
        return $this->postRepository->findById($post->id) ?? $post->refresh();
    }

    public function deletePost(Post $post): bool
    {
        return $this->postRepository->delete($post);
    }
}

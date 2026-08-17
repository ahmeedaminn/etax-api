<?php

namespace App\Services\Post;

use App\Repositories\Interfaces\Post\PostRepositoryInterface;

class PostService
{
    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function createPost(array $data)
    {
        return $this->postRepository->create($data);
    }

    public function getPostById($postId)
    {
        // fetch post by its id
        return \App\Models\Post::with(['user', 'files'])
        ->where('id', $postId)
        ->get();
    }

    public function getPostsByCategory($categoryId)
    {
        // Fetch posts for this category, and eager-load the user and files!
        return \App\Models\Post::with(['user', 'files'])
            ->where('category_id', $categoryId)
            ->latest()
            ->get();
    }
}

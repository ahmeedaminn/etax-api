<?php

namespace App\Repositories\Eloquent\Post;

use App\Repositories\Interfaces\Post\PostRepositoryInterface;
use App\Models\Post;

class PostRepository implements PostRepositoryInterface
{
    public function create(array $data)
    {
        return Post::create($data);
    }
}
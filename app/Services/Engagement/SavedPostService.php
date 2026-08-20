<?php

namespace App\Services\Engagement;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Repositories\Interfaces\Engagement\SavedPostRepositoryInterface;



class SavedPostService
{
    public function __construct(
        protected SavedPostRepositoryInterface $savedPostRepository
    ) {
    }

    public function savePost(User $user, Post $post): void
    {
        $this->savedPostRepository->save($user->id, $post->id);
    }

    public function unsavePost(User $user, Post $post): void
    {
        $this->savedPostRepository->remove($user->id, $post->id);
    }

}
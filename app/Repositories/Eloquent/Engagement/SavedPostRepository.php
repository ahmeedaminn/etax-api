<?php

namespace App\Repositories\Eloquent\Engagement;

use App\Models\SavedPost;
use App\Repositories\Interfaces\Engagement\SavedPostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SavedPostRepository implements SavedPostRepositoryInterface
{
    public function save(int $userId, int $postId): SavedPost
    {
        return SavedPost::firstOrCreate([
            'user_id' => $userId,
            'post_id' => $postId,
        ]);
    }

    public function remove(int $userId, int $postId): bool
    {
        return SavedPost::query()
            ->where('user_id', $userId)
            ->where('post_id', $postId)
            ->delete() > 0;
    }

    public function forUser(int $userId): Collection
    {
        return SavedPost::query()
            ->with(['post.institution.institutionProfile', 'post.category'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}

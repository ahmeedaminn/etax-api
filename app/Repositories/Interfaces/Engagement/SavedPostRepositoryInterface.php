<?php

namespace App\Repositories\Interfaces\Engagement;

use App\Models\SavedPost;
use Illuminate\Database\Eloquent\Collection;

interface SavedPostRepositoryInterface
{
    public function save(int $userId, int $postId): SavedPost;

    public function remove(int $userId, int $postId): bool;

    public function forUser(int $userId): Collection;
}

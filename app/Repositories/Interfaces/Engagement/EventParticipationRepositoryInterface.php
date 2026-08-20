<?php

namespace App\Repositories\Interfaces\Engagement;

use App\Enums\ParticipationStatus;
use App\Models\EventParticipation;
use Illuminate\Database\Eloquent\Collection;

interface EventParticipationRepositoryInterface
{
    public function setStatus(int $userId, int $postId, ParticipationStatus $status): EventParticipation;

    public function remove(int $userId, int $postId): bool;

    public function forUser(int $userId): Collection;
}

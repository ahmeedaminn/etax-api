<?php

namespace App\Repositories\Eloquent\Engagement;

use App\Enums\ParticipationStatus;
use App\Models\EventParticipation;
use App\Repositories\Interfaces\Engagement\EventParticipationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EventParticipationRepository implements EventParticipationRepositoryInterface
{
    public function setStatus(int $userId, int $postId, ParticipationStatus $status): EventParticipation
    {
        return EventParticipation::updateOrCreate(
            ['user_id' => $userId, 'post_id' => $postId],
            ['status' => $status],
        );
    }

    public function remove(int $userId, int $postId): bool
    {
        return EventParticipation::query()
            ->where('user_id', $userId)
            ->where('post_id', $postId)
            ->delete() > 0;
    }

    public function forUser(int $userId): Collection
    {
        return EventParticipation::query()
            ->with(['post.institution.institutionProfile', 'post.category'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}

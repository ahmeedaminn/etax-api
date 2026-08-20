<?php

namespace App\Repositories\Eloquent\Statistics;

use App\Enums\InstitutionRequestStatus;
use App\Enums\ParticipationStatus;
use App\Enums\PostType;
use App\Enums\UserRole;
use App\Models\EventParticipation;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\User;
use App\Repositories\Interfaces\Statistics\StatisticsRepositoryInterface;

class StatisticsRepository implements StatisticsRepositoryInterface
{
    public function forInstitution(int $institutionId): array
    {
        return [
            'posts_count' => Post::query()
                ->where('institution_id', $institutionId)
                ->count(),
            'events_count' => Post::query()
                ->where('institution_id', $institutionId)
                ->where('type', PostType::EVENT->value)
                ->count(),
            'announcements_count' => Post::query()
                ->where('institution_id', $institutionId)
                ->where('type', PostType::ANNOUNCEMENT->value)
                ->count(),
            'interested_count' => $this->participationCountForInstitution(
                $institutionId,
                ParticipationStatus::INTERESTED,
            ),
            'going_count' => $this->participationCountForInstitution(
                $institutionId,
                ParticipationStatus::GOING,
            ),
            'saved_count' => SavedPost::query()
                ->whereHas('post', fn ($query) => $query
                    ->where('institution_id', $institutionId))
                ->count(),
        ];
    }

    public function forPlatform(): array
    {
        return [
            'users_count' => User::query()->count(),
            'institutions_count' => User::query()
                ->where('role', UserRole::INSTITUTION->value)
                ->where(
                    'institution_request_status',
                    InstitutionRequestStatus::APPROVED->value,
                )
                ->count(),
            'posts_count' => Post::query()->count(),
            'events_count' => Post::query()
                ->where('type', PostType::EVENT->value)
                ->count(),
            'announcements_count' => Post::query()
                ->where('type', PostType::ANNOUNCEMENT->value)
                ->count(),
            'interested_count' => EventParticipation::query()
                ->where('status', ParticipationStatus::INTERESTED->value)
                ->count(),
            'going_count' => EventParticipation::query()
                ->where('status', ParticipationStatus::GOING->value)
                ->count(),
            'saved_count' => SavedPost::query()->count(),
            'pending_institution_requests_count' => User::query()
                ->where(
                    'institution_request_status',
                    InstitutionRequestStatus::PENDING->value,
                )
                ->count(),
        ];
    }

    private function participationCountForInstitution(
        int $institutionId,
        ParticipationStatus $status,
    ): int {
        return EventParticipation::query()
            ->where('status', $status->value)
            ->whereHas('post', fn ($query) => $query
                ->where('institution_id', $institutionId))
            ->count();
    }
}

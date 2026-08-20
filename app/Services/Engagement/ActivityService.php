<?php

namespace App\Services\Engagement;

use App\Enums\ParticipationStatus;
use App\Models\EventParticipation;
use App\Models\SavedPost;
use App\Models\User;
use App\Repositories\Interfaces\Engagement\EventParticipationRepositoryInterface;
use App\Repositories\Interfaces\Engagement\SavedPostRepositoryInterface;

class ActivityService
{
    public function __construct(
        protected EventParticipationRepositoryInterface $participationRepository,
        protected SavedPostRepositoryInterface $savedPostRepository,
    ) {
    }

    public function getUserActivity(User $user): array
    {
        $participations = $this->participationRepository->forUser($user->id);
        $savedPosts = $this->savedPostRepository->forUser($user->id);

        return [
            'interested' => $participations
                ->filter(
                    fn (EventParticipation $participation) =>
                    $participation->status === ParticipationStatus::INTERESTED
                )
                ->pluck('post')
                ->values(),

            'going' => $participations
                ->filter(
                    fn (EventParticipation $participation) =>
                    $participation->status === ParticipationStatus::GOING
                )
                ->pluck('post')
                ->values(),

            'saved' => $savedPosts
                ->map(
                    fn (SavedPost $savedPost) => $savedPost->post
                )
                ->values(),
        ];
    }
}
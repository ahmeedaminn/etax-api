<?php

namespace App\Services\Engagement;

use App\Enums\ParticipationStatus;
use App\Enums\PostType;
use App\Models\EventParticipation;
use App\Models\Post;
use App\Models\User;
use App\Repositories\Interfaces\Engagement\EventParticipationRepositoryInterface;
use Illuminate\Validation\ValidationException;

class EventParticipationService
{
    public function __construct(
        protected EventParticipationRepositoryInterface $participationRepository
    ) {}

    public function setParticipation(
        User $user,
        Post $post,
        ParticipationStatus $status
    ): EventParticipation {
        $this->ensurePostIsEvent($post);

        return $this->participationRepository->setStatus(
            $user->id,
            $post->id,
            $status,
        );
    }

    public function removeParticipation(
        User $user,
        Post $post
    ): bool {
        $this->ensurePostIsEvent($post);

        return $this->participationRepository->remove(
            $user->id,
            $post->id,
        );
    }

    private function ensurePostIsEvent(Post $post): void
    {
        if ($post->type !== PostType::EVENT) {
            throw ValidationException::withMessages([
                'post' => [
                    'Participation is available only for Events.',
                ],
            ]);
        }
    }
}

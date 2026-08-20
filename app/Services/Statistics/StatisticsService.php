<?php

namespace App\Services\Statistics;

use App\Enums\InstitutionRequestStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Interfaces\Statistics\StatisticsRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class StatisticsService
{
    public function __construct(
        protected StatisticsRepositoryInterface $statisticsRepository,
    ) {}

    public function getInstitutionStatistics(User $institution): array
    {
        if ($institution->role !== UserRole::INSTITUTION
            || $institution->institution_request_status !== InstitutionRequestStatus::APPROVED) {
            throw new AuthorizationException(
                'Only approved Institutions can view Institution statistics.',
            );
        }

        return $this->statisticsRepository->forInstitution($institution->id);
    }

    public function getPlatformStatistics(): array
    {
        return $this->statisticsRepository->forPlatform();
    }
}

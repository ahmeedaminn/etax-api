<?php

namespace App\Repositories\Interfaces\Statistics;

interface StatisticsRepositoryInterface
{
    public function forInstitution(int $institutionId): array;

    public function forPlatform(): array;
}

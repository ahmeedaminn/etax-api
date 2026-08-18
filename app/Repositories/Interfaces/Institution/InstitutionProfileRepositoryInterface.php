<?php

namespace App\Repositories\Interfaces\Institution;

use App\Models\InstitutionProfile;

interface InstitutionProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?InstitutionProfile;

    public function create(array $data): InstitutionProfile;

    public function update(InstitutionProfile $profile, array $data): bool;
}

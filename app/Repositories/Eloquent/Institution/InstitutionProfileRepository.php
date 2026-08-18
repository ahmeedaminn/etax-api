<?php

namespace App\Repositories\Eloquent\Institution;

use App\Models\InstitutionProfile;
use App\Repositories\Interfaces\Institution\InstitutionProfileRepositoryInterface;

class InstitutionProfileRepository implements InstitutionProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?InstitutionProfile
    {
        return InstitutionProfile::query()
            ->with(['user', 'logo'])
            ->where('user_id', $userId)
            ->first();
    }

    public function create(array $data): InstitutionProfile
    {
        return InstitutionProfile::create($data);
    }

    public function update(InstitutionProfile $profile, array $data): bool
    {
        return $profile->update($data);
    }
}

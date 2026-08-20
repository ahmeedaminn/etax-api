<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\InstitutionProfile;
use App\Models\User;

class InstitutionProfilePolicy
{
    public function attachFile(User $user, InstitutionProfile $profile): bool
    {
        return $profile->user_id === $user->id || $user->role === UserRole::ADMIN;
    }
}

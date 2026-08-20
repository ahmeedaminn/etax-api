<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function attachFile(User $user, User $targetUser): bool
    {
        return $user->id === $targetUser->id || $user->role === UserRole::ADMIN;
    }
}

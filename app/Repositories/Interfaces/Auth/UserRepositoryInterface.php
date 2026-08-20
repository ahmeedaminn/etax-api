<?php

namespace App\Repositories\Interfaces\Auth;

use App\Enums\InstitutionRequestStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    public function updatePassword(User $user, string $newPassword): bool;

    public function updateUserProfile(User $user, array $data): bool;

    public function getPendingInstitutionApplications(): Collection;

    public function updateRoleAndRequestStatus(
        User $user,
        UserRole $role,
        InstitutionRequestStatus $requestStatus,
    ): bool;
}

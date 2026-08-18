<?php

namespace App\Repositories\Interfaces\Auth;

use App\Enums\InstitutionRequestStatus;
use App\Enums\UserRole;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    public function updatePassword(User $user, string $newPassword): bool;

    public function updateRoleAndRequestStatus(
        User $user,
        UserRole $role,
        InstitutionRequestStatus $requestStatus,
    ): bool;
}

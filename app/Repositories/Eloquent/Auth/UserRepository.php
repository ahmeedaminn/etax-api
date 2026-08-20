<?php

namespace App\Repositories\Eloquent\Auth; // <-- THIS MUST MATCH THE FOLDERS

use App\Enums\InstitutionRequestStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Interfaces\Auth\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'institution_request_status' => $data['institution_request_status']
                ?? InstitutionRequestStatus::NONE,
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function updatePassword(User $user, string $newPassword): bool
    {
        return $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function updateRoleAndRequestStatus(
        User $user,
        UserRole $role,
        InstitutionRequestStatus $requestStatus,
    ): bool {
        return $user->update([
            'role' => $role,
            'institution_request_status' => $requestStatus,
        ]);
    }
}

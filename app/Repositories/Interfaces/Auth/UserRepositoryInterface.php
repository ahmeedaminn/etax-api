<?php 

namespace App\Repositories\Interfaces\Auth;

use App\Models\User;

interface UserRepositoryInterface 
{
    // : User -> means that this function will return class from type User (Models/User)
    public function create(array $data): User;
    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;
    public function updatePassword(User $user, string $newPassword): bool;
}


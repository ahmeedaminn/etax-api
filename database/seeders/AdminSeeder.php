<?php

namespace Database\Seeders;

use App\Enums\InstitutionRequestStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('admin.password');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException(
                'Set ADMIN_PASSWORD in the .env file before running AdminSeeder.',
            );
        }

        User::updateOrCreate(
            ['email' => config('admin.email')],
            [
                'name' => config('admin.name'),
                'password' => $password,
                'role' => UserRole::ADMIN,
                'institution_request_status' => InstitutionRequestStatus::NONE,
            ],
        );
    }
}

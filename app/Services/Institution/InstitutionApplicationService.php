<?php

namespace App\Services\Institution;

use App\Enums\InstitutionRequestStatus;
use App\Enums\UserRole;
use App\Models\InstitutionProfile;
use App\Models\User;
use App\Repositories\Interfaces\Auth\UserRepositoryInterface;
use App\Repositories\Interfaces\Institution\InstitutionProfileRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstitutionApplicationService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected InstitutionProfileRepositoryInterface $institutionProfileRepository,
    ) {}

    public function submitApplication(User $user, array $data): User
    {
        if ($user->role !== UserRole::USER) {
            throw ValidationException::withMessages([
                'application' => ['Only regular users can apply to become an Institution.'],
            ]);
        }

        if (! in_array($user->institution_request_status, [
            InstitutionRequestStatus::NONE,
            InstitutionRequestStatus::REJECTED,
        ], true)) {
            throw ValidationException::withMessages([
                'application' => ['This user already has an active Institution application.'],
            ]);
        }

        return DB::transaction(function () use ($user, $data): User {
            $profile = $this->institutionProfileRepository->findByUserId($user->id);

            if ($profile) {
                // A rejected applicant may correct the existing profile and reapply.
                $this->institutionProfileRepository->update($profile, $data);
            } else {
                $this->institutionProfileRepository->create([
                    ...$data,
                    'user_id' => $user->id,
                ]);
            }

            $this->userRepository->updateRoleAndRequestStatus(
                $user,
                UserRole::USER,
                InstitutionRequestStatus::PENDING,
            );

            return $user->refresh()->load('institutionProfile.logo');
        });
    }

    public function getApplication(User $user): User
    {
        $this->findProfileOrFail($user);

        return $user->refresh()->load('institutionProfile.logo');
    }

    public function updateApplication(User $user, array $data): User
    {
        $profile = $this->findProfileOrFail($user);

        $this->institutionProfileRepository->update($profile, $data);

        return $user->refresh()->load('institutionProfile.logo');
    }

    public function getPendingApplications(): Collection
    {
        return $this->userRepository->getPendingInstitutionApplications();
    }

    public function reviewApplication(
        User $applicant,
        InstitutionRequestStatus $status,
    ): User {
        $currentStatus = $applicant->institution_request_status;
        $newStatus = $status;

        if (! in_array($newStatus, [
            InstitutionRequestStatus::APPROVED,
            InstitutionRequestStatus::REJECTED,
        ], true)) {
            throw ValidationException::withMessages([
                'status' => ['The status must be APPROVED or REJECTED.'],
            ]);
        }

        if ($currentStatus !== InstitutionRequestStatus::PENDING) {
            throw ValidationException::withMessages([
                'application' => ['Only pending Institution applications can be reviewed.'],
            ]);
        }

        $this->findProfileOrFail($applicant);

        $role = $newStatus === InstitutionRequestStatus::APPROVED
            ? UserRole::INSTITUTION
            : UserRole::USER;

        $this->userRepository->updateRoleAndRequestStatus(
            $applicant,
            $role,
            $newStatus,
        );

        return $applicant->refresh()->load('institutionProfile.logo');
    }

    private function findProfileOrFail(User $user): InstitutionProfile
    {
        $profile = $this->institutionProfileRepository->findByUserId($user->id);

        if (! $profile) {
            throw (new ModelNotFoundException)->setModel(InstitutionProfile::class);
        }

        return $profile;
    }
}

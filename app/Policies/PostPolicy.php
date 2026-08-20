<?php

namespace App\Policies;

use App\Enums\InstitutionRequestStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::INSTITUTION
            && $user->institution_request_status ===
            InstitutionRequestStatus::APPROVED;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        // Institutions may modify only their own posts after approval.
        return $user->role === UserRole::INSTITUTION
            && $user->institution_request_status
            === InstitutionRequestStatus::APPROVED
            && $post->institution_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    public function attachFile(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }
}

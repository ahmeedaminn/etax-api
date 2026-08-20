<?php

namespace Tests\Feature;

use App\Enums\InstitutionRequestStatus;
use App\Enums\UserRole;
use App\Models\InstitutionProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_view_and_update_an_institution_application(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->postJson('/api/institution/application', $this->applicationData())
            ->assertCreated()
            ->assertJsonPath('data.role', UserRole::USER->value)
            ->assertJsonPath(
                'data.institution_request_status',
                InstitutionRequestStatus::PENDING->value,
            )
            ->assertJsonPath(
                'data.institution_profile.organization_name',
                'Community Builders',
            );

        $this->assertDatabaseHas('institution_profiles', [
            'user_id' => $user->id,
            'organization_name' => 'Community Builders',
        ]);

        $this->actingAs($user, 'api')
            ->getJson('/api/institution/application')
            ->assertOk()
            ->assertJsonPath(
                'data.institution_profile.organization_name',
                'Community Builders',
            );

        $this->actingAs($user, 'api')
            ->patchJson('/api/institution/application', [
                'organization_name' => 'Community Builders Egypt',
            ])
            ->assertOk()
            ->assertJsonPath(
                'data.institution_profile.organization_name',
                'Community Builders Egypt',
            );
    }

    public function test_user_cannot_submit_a_second_active_application(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->postJson('/api/institution/application', $this->applicationData())
            ->assertCreated();

        $this->actingAs($user->refresh(), 'api')
            ->postJson('/api/institution/application', $this->applicationData())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('application');

        $this->assertSame(
            1,
            InstitutionProfile::query()->where('user_id', $user->id)->count(),
        );
    }

    public function test_admin_can_list_and_approve_pending_applications(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $applicant = $this->createPendingApplicant();

        $this->actingAs($admin, 'api')
            ->getJson('/api/admin/institution-applications')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $applicant->id);

        $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/institution-applications/{$applicant->id}", [
                'status' => InstitutionRequestStatus::APPROVED->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.role', UserRole::INSTITUTION->value)
            ->assertJsonPath(
                'data.institution_request_status',
                InstitutionRequestStatus::APPROVED->value,
            );

        $this->assertDatabaseHas('users', [
            'id' => $applicant->id,
            'role' => UserRole::INSTITUTION->value,
            'institution_request_status' => InstitutionRequestStatus::APPROVED->value,
        ]);
    }

    public function test_admin_can_reject_a_pending_application(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $applicant = $this->createPendingApplicant();

        $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/institution-applications/{$applicant->id}", [
                'status' => InstitutionRequestStatus::REJECTED->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.role', UserRole::USER->value)
            ->assertJsonPath(
                'data.institution_request_status',
                InstitutionRequestStatus::REJECTED->value,
            );
    }

    public function test_non_admin_cannot_review_an_application(): void
    {
        $user = User::factory()->create();
        $applicant = $this->createPendingApplicant();

        $this->actingAs($user, 'api')
            ->patchJson("/api/admin/institution-applications/{$applicant->id}", [
                'status' => InstitutionRequestStatus::APPROVED->value,
            ])
            ->assertForbidden();
    }

    private function createPendingApplicant(): User
    {
        $applicant = User::factory()->create([
            'role' => UserRole::USER,
            'institution_request_status' => InstitutionRequestStatus::PENDING,
        ]);

        InstitutionProfile::create([
            ...$this->applicationData(),
            'user_id' => $applicant->id,
        ]);

        return $applicant;
    }

    private function applicationData(): array
    {
        return [
            'organization_name' => 'Community Builders',
            'description' => 'We organize local educational events.',
            'website' => 'https://example.com',
            'contact_email' => 'contact@example.com',
            'contact_phone' => '+201000000000',
            'location' => 'Cairo',
        ];
    }
}

<?php

namespace Tests\Feature;

use App\Enums\InstitutionRequestStatus;
use App\Enums\ParticipationStatus;
use App\Enums\PostType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\EventParticipation;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_responses_include_participation_and_saved_counts(): void
    {
        $category = Category::create(['name' => 'Technology']);
        $institution = $this->createInstitution();
        $viewer = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = $this->createPost($institution, $category, PostType::EVENT);

        EventParticipation::create([
            'user_id' => $viewer->id,
            'post_id' => $post->id,
            'status' => ParticipationStatus::INTERESTED,
        ]);
        EventParticipation::create([
            'user_id' => $otherUser->id,
            'post_id' => $post->id,
            'status' => ParticipationStatus::GOING,
        ]);
        SavedPost::create([
            'user_id' => $viewer->id,
            'post_id' => $post->id,
        ]);

        $this->actingAs($viewer, 'api')
            ->getJson("/api/categories/{$category->id}/posts")
            ->assertOk()
            ->assertJsonPath('data.0.interested_count', 1)
            ->assertJsonPath('data.0.going_count', 1)
            ->assertJsonPath('data.0.saved_count', 1);
    }

    public function test_institution_statistics_include_only_its_own_posts(): void
    {
        $category = Category::create(['name' => 'Education']);
        $institution = $this->createInstitution();
        $otherInstitution = $this->createInstitution();
        $user = User::factory()->create();

        $event = $this->createPost($institution, $category, PostType::EVENT);
        $announcement = $this->createPost($institution, $category, PostType::ANNOUNCEMENT);
        $otherEvent = $this->createPost($otherInstitution, $category, PostType::EVENT);

        EventParticipation::create([
            'user_id' => $user->id,
            'post_id' => $event->id,
            'status' => ParticipationStatus::GOING,
        ]);
        EventParticipation::create([
            'user_id' => $user->id,
            'post_id' => $otherEvent->id,
            'status' => ParticipationStatus::INTERESTED,
        ]);
        SavedPost::create(['user_id' => $user->id, 'post_id' => $event->id]);
        SavedPost::create(['user_id' => $user->id, 'post_id' => $announcement->id]);
        SavedPost::create(['user_id' => $user->id, 'post_id' => $otherEvent->id]);

        $this->actingAs($institution, 'api')
            ->getJson('/api/institution/statistics')
            ->assertOk()
            ->assertJsonPath('data.posts_count', 2)
            ->assertJsonPath('data.events_count', 1)
            ->assertJsonPath('data.announcements_count', 1)
            ->assertJsonPath('data.interested_count', 0)
            ->assertJsonPath('data.going_count', 1)
            ->assertJsonPath('data.saved_count', 2);
    }

    public function test_admin_statistics_include_platform_totals(): void
    {
        $category = Category::create(['name' => 'Business']);
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $institution = $this->createInstitution();
        $user = User::factory()->create();
        User::factory()->create([
            'institution_request_status' => InstitutionRequestStatus::PENDING,
        ]);

        $event = $this->createPost($institution, $category, PostType::EVENT);
        $this->createPost($institution, $category, PostType::ANNOUNCEMENT);

        EventParticipation::create([
            'user_id' => $user->id,
            'post_id' => $event->id,
            'status' => ParticipationStatus::INTERESTED,
        ]);
        SavedPost::create(['user_id' => $user->id, 'post_id' => $event->id]);

        $this->actingAs($admin, 'api')
            ->getJson('/api/admin/statistics')
            ->assertOk()
            ->assertJsonPath('data.users_count', 4)
            ->assertJsonPath('data.institutions_count', 1)
            ->assertJsonPath('data.posts_count', 2)
            ->assertJsonPath('data.events_count', 1)
            ->assertJsonPath('data.announcements_count', 1)
            ->assertJsonPath('data.interested_count', 1)
            ->assertJsonPath('data.going_count', 0)
            ->assertJsonPath('data.saved_count', 1)
            ->assertJsonPath('data.pending_institution_requests_count', 1);
    }

    public function test_statistics_routes_reject_the_wrong_roles(): void
    {
        $normalUser = User::factory()->create();
        $pendingInstitution = User::factory()->create([
            'role' => UserRole::INSTITUTION,
            'institution_request_status' => InstitutionRequestStatus::PENDING,
        ]);

        $this->actingAs($normalUser, 'api')
            ->getJson('/api/admin/statistics')
            ->assertForbidden();

        $this->actingAs($pendingInstitution, 'api')
            ->getJson('/api/institution/statistics')
            ->assertForbidden();
    }

    private function createInstitution(): User
    {
        return User::factory()->create([
            'role' => UserRole::INSTITUTION,
            'institution_request_status' => InstitutionRequestStatus::APPROVED,
        ]);
    }

    private function createPost(
        User $institution,
        Category $category,
        PostType $type,
    ): Post {
        return Post::create([
            'institution_id' => $institution->id,
            'category_id' => $category->id,
            'type' => $type,
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
        ]);
    }
}

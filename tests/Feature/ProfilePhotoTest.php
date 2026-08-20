<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_own_profile_picture_and_receive_it_from_me(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->post('/api/drive/upload', [
                'file' => UploadedFile::fake()->image('profile.jpg'),
                'fileable_id' => $user->id,
                'fileable_type' => User::class,
            ], ['Accept' => 'application/json'])
            ->assertCreated();

        $this->actingAs($user, 'api')
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('profile_picture.file_name', 'profile.jpg')
            ->assertJsonStructure([
                'profile_picture' => ['id', 'url'],
            ]);
    }

    public function test_user_cannot_upload_a_profile_picture_for_another_user(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user, 'api')
            ->post('/api/drive/upload', [
                'file' => UploadedFile::fake()->image('not-mine.jpg'),
                'fileable_id' => $otherUser->id,
                'fileable_type' => User::class,
            ], ['Accept' => 'application/json'])
            ->assertForbidden();

        $this->assertDatabaseCount('files', 0);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileImageApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app.url', 'http://localhost');
        Config::set('filesystems.default', 'public');
        Config::set('filesystems.disks.public.url', 'http://localhost/storage');
        Config::set('profile.image_disk', 'public');

        Storage::fake('public');
    }

    public function test_profile_endpoint_returns_user_data_with_image_url(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/profile');

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'profile_image', 'profile_image_url'],
            ]);
    }

    public function test_user_can_upload_profile_image(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('avatar.png', 800, 800);

        $response = $this->postJson('/api/profile/image', [
            'image' => $file,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Profile image updated successfully.');

        $user->refresh();

        $this->assertNotNull($user->profile_image);
        Storage::assertExists($user->profile_image);
        $this->assertStringContainsString('/storage/', $response->json('data.profile_image_url'));
    }

    public function test_profile_image_validation_rejects_invalid_file_type(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');

        $response = $this->postJson('/api/profile/image', [
            'image' => $file,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('image');
    }

    public function test_updating_profile_image_deletes_old_file(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $firstUpload = $this->postJson('/api/profile/image', [
            'image' => UploadedFile::fake()->image('first.png', 600, 600),
        ]);

        $firstUpload->assertOk();

        $user->refresh();
        $oldPath = $user->profile_image;

        Storage::assertExists($oldPath);

        $secondUpload = $this->postJson('/api/profile/image', [
            'image' => UploadedFile::fake()->image('second.png', 600, 600),
        ]);

        $secondUpload->assertOk();

        $user->refresh();

        $this->assertNotSame($oldPath, $user->profile_image);
        Storage::assertMissing($oldPath);
        Storage::assertExists($user->profile_image);
    }

    public function test_user_can_remove_profile_image(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $upload = $this->postJson('/api/profile/image', [
            'image' => UploadedFile::fake()->image('avatar.png', 600, 600),
        ]);

        $upload->assertOk();

        $user->refresh();
        $oldPath = $user->profile_image;
        Storage::assertExists($oldPath);

        $response = $this->deleteJson('/api/profile/image');

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Profile image removed successfully.')
            ->assertJsonPath('data.profile_image', null);

        $user->refresh();

        $this->assertNull($user->profile_image);
        Storage::assertMissing($oldPath);
    }

    public function test_removing_profile_image_when_none_exists_still_succeeds(): void
    {
        $user = User::factory()->create([
            'profile_image' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/profile/image');

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Profile image removed successfully.')
            ->assertJsonPath('data.profile_image', null);
    }
}

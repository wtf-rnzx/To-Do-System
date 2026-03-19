<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_management_supports_role_multi_select_and_date_range_filters(): void
    {
        $admin = User::factory()->create();
        $admin->usertype = 'admin';
        $admin->save();

        $adminUser = User::factory()->create([
            'name' => 'Admin Role Match',
            'email' => 'admin-role@example.com',
        ]);
        $adminUser->usertype = 'admin';
        $adminUser->save();

        $regularUser = User::factory()->create([
            'name' => 'Regular User Match',
            'email' => 'regular-role@example.com',
        ]);
        $regularUser->usertype = 'user';
        $regularUser->save();

        DB::table('users')->where('id', $adminUser->id)->update([
            'created_at' => '2026-03-10 08:00:00',
        ]);

        DB::table('users')->where('id', $regularUser->id)->update([
            'created_at' => '2026-03-11 08:00:00',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'roles' => ['admin'],
            'from' => '2026-03-09',
            'to' => '2026-03-10',
        ]));

        $response->assertOk();
        $response->assertSeeText('Admin Role Match');
        $response->assertDontSeeText('Regular User Match');
    }

    public function test_activity_logs_use_centralized_filters_and_ignore_search_param(): void
    {
        $admin = User::factory()->create();
        $admin->usertype = 'admin';
        $admin->save();

        $targetUser = User::factory()->create([
            'name' => 'Target Person',
            'email' => 'target-person@example.com',
        ]);

        $matchingLog = ActivityLog::create([
            'user_id' => $targetUser->id,
            'user_name' => $targetUser->name,
            'action' => 'created',
            'module' => 'users',
            'description' => 'Created a new user record',
            'ip_address' => '127.0.0.1',
        ]);

        $nonMatchingLog = ActivityLog::create([
            'user_id' => $targetUser->id,
            'user_name' => $targetUser->name,
            'action' => 'deleted',
            'module' => 'todos',
            'description' => 'Deleted a todo item',
            'ip_address' => '127.0.0.2',
        ]);

        DB::table('activity_logs')->where('id', $matchingLog->id)->update([
            'created_at' => '2026-03-10 09:00:00',
        ]);

        DB::table('activity_logs')->where('id', $nonMatchingLog->id)->update([
            'created_at' => '2026-03-11 09:00:00',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.logs.index', [
            'user_id' => $targetUser->id,
            'actions' => ['created'],
            'modules' => ['users'],
            'sort' => 'newest',
            'from' => '2026-03-09',
            'to' => '2026-03-10',
            'search' => 'Deleted',
        ]));

        $response->assertOk();
        $response->assertSeeText('Created a new user record');
        $response->assertDontSeeText('Deleted a todo item');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Todo;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_achievements_page_displays_and_syncs_20_definitions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('achievements.index'));

        $response->assertOk();
        $this->assertSame(20, Achievement::query()->count());
    }

    public function test_unlock_is_tracked_once_without_duplicates(): void
    {
        $user = User::factory()->create();

        $todo = Todo::create([
            'title' => 'First completion',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
        ]);

        $this->actingAs($user);

        $todo->update(['completed' => true]);
        $todo->update(['completed' => false]);
        $todo->update(['completed' => true]);

        $firstWin = Achievement::query()->where('slug', 'first_task_completed')->firstOrFail();

        $count = UserAchievement::query()
            ->where('user_id', $user->id)
            ->where('achievement_id', $firstWin->id)
            ->whereNotNull('unlocked_at')
            ->count();

        $this->assertSame(1, $count);
    }

    public function test_user_can_toggle_profile_visibility_for_unlocked_achievement(): void
    {
        $user = User::factory()->create();

        $todo = Todo::create([
            'title' => 'Visibility unlock task',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
        ]);

        $this->actingAs($user);
        $todo->update(['completed' => true]);

        $firstWin = Achievement::query()->where('slug', 'first_task_completed')->firstOrFail();

        $userAchievement = UserAchievement::query()
            ->where('user_id', $user->id)
            ->where('achievement_id', $firstWin->id)
            ->firstOrFail();

        $this->assertTrue($userAchievement->is_visible);

        $this->patch(route('achievements.toggle-visibility', $firstWin));

        $this->assertFalse($userAchievement->fresh()->is_visible);
    }

    public function test_clean_week_and_month_are_not_unlocked_for_new_user_without_due_date_history(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('achievements.index'));

        $response->assertOk();

        $cleanWeek = Achievement::query()->where('slug', 'no_overdue_week')->firstOrFail();
        $cleanMonth = Achievement::query()->where('slug', 'no_overdue_month')->firstOrFail();

        $cleanWeekRow = UserAchievement::query()
            ->where('user_id', $user->id)
            ->where('achievement_id', $cleanWeek->id)
            ->firstOrFail();

        $cleanMonthRow = UserAchievement::query()
            ->where('user_id', $user->id)
            ->where('achievement_id', $cleanMonth->id)
            ->firstOrFail();

        $this->assertNull($cleanWeekRow->unlocked_at);
        $this->assertNull($cleanMonthRow->unlocked_at);
        $this->assertSame(0, (int) $cleanWeekRow->progress);
        $this->assertSame(0, (int) $cleanMonthRow->progress);
    }

}

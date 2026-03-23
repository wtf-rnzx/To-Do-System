<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('experience.mode', 'fixed');
        config()->set('experience.base_points', 20);
        config()->set('experience.ranks', [
            [
                'key' => 'novice',
                'name' => 'Novice',
                'min_exp' => 0,
                'badge' => '🌱',
            ],
            [
                'key' => 'apprentice',
                'name' => 'Apprentice',
                'min_exp' => 20,
                'badge' => '🧭',
            ],
            [
                'key' => 'pro',
                'name' => 'Pro',
                'min_exp' => 40,
                'badge' => '🏆',
            ],
        ]);
    }

    public function test_awards_exp_immediately_when_task_is_completed(): void
    {
        $user = User::factory()->create(['usertype' => 'user']);

        $todo = Todo::create([
            'title' => 'Immediate exp task',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
        ]);

        $this->actingAs($user);

        $todo->update(['completed' => true]);

        $user->refresh();
        $todo->refresh();

        $this->assertSame(20, (int) $user->total_exp);
        $this->assertSame('apprentice', $user->current_rank);
        $this->assertSame(0, (int) $user->rank_progress_pct);
        $this->assertNotNull($todo->exp_awarded_at);
    }

    public function test_prevents_exp_double_counting_for_same_todo(): void
    {
        $user = User::factory()->create(['usertype' => 'user']);

        $todo = Todo::create([
            'title' => 'No abuse task',
            'user_id' => $user->id,
            'priority' => 'high',
            'completed' => false,
        ]);

        $this->actingAs($user);

        $todo->update(['completed' => true]);
        $todo->update(['completed' => false]);
        $todo->update(['completed' => true]);

        $user->refresh();
        $todo->refresh();

        $this->assertSame(20, (int) $user->total_exp, 'EXP should only be awarded once per todo.');
        $this->assertNotNull($todo->exp_awarded_at);
    }

    public function test_rank_progresses_when_total_exp_crosses_threshold(): void
    {
        $user = User::factory()->create(['usertype' => 'user']);

        $first = Todo::create([
            'title' => 'Rank step one',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
        ]);

        $second = Todo::create([
            'title' => 'Rank step two',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
        ]);

        $this->actingAs($user);

        $first->update(['completed' => true]);
        $second->update(['completed' => true]);

        $user->refresh();

        $this->assertSame(40, (int) $user->total_exp);
        $this->assertSame('pro', $user->current_rank);
        $this->assertSame(100, (int) $user->rank_progress_pct);
    }

    public function test_dashboard_and_profile_show_experience_information(): void
    {
        $user = User::factory()->create(['usertype' => 'user']);

        $todo = Todo::create([
            'title' => 'UI exp task',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
        ]);

        $this->actingAs($user);
        $todo->update(['completed' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Experience & Rank')
            ->assertSee('20 EXP')
            ->assertSee('Apprentice')
            ->assertSee('Rank Roadmap')
            ->assertSee('Requires at least 20 EXP')
            ->assertSee('Current');

        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Your Experience')
            ->assertSee('20 EXP')
            ->assertSee('Apprentice')
            ->assertSee('Rank Roadmap')
            ->assertSee('Requires at least 20 EXP')
            ->assertSee('Current');
    }
}

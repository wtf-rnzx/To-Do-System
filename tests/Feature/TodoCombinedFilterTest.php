<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoCombinedFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_combined_filter_applies_status_filter(): void
    {
        $user = User::factory()->create();

        Todo::create([
            'title' => 'Completed Task Filter Target',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => true,
        ]);

        Todo::create([
            'title' => 'Ongoing Task Should Be Hidden',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
        ]);

        $response = $this->actingAs($user)->get(route('todos.index', [
            'filter' => 'status:completed',
        ]));

        $response->assertOk();
        $response->assertSeeText('Completed Task Filter Target');
        $response->assertDontSeeText('Ongoing Task Should Be Hidden');
    }

    public function test_combined_filter_applies_priority_filter(): void
    {
        $user = User::factory()->create();

        Todo::create([
            'title' => 'High Priority Filter Target',
            'user_id' => $user->id,
            'priority' => 'high',
            'completed' => false,
        ]);

        Todo::create([
            'title' => 'Low Priority Should Be Hidden',
            'user_id' => $user->id,
            'priority' => 'low',
            'completed' => false,
        ]);

        $response = $this->actingAs($user)->get(route('todos.index', [
            'filter' => 'priority:high',
        ]));

        $response->assertOk();
        $response->assertSeeText('High Priority Filter Target');
        $response->assertDontSeeText('Low Priority Should Be Hidden');
    }

    public function test_combined_filter_applies_smart_view_filter(): void
    {
        $user = User::factory()->create();

        Todo::create([
            'title' => 'Today Smart Filter Target',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
            'due_date' => today(),
        ]);

        Todo::create([
            'title' => 'Upcoming Should Be Hidden',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
            'due_date' => today()->addDay(),
        ]);

        $response = $this->actingAs($user)->get(route('todos.index', [
            'filter' => 'smart:today',
        ]));

        $response->assertOk();
        $response->assertSeeText('Today Smart Filter Target');
        $response->assertDontSeeText('Upcoming Should Be Hidden');
    }

    public function test_checkbox_multi_select_priority_filters_apply_as_where_in(): void
    {
        $user = User::factory()->create();

        Todo::create([
            'title' => 'High Priority Visible',
            'user_id' => $user->id,
            'priority' => 'high',
            'completed' => false,
        ]);

        Todo::create([
            'title' => 'Low Priority Visible',
            'user_id' => $user->id,
            'priority' => 'low',
            'completed' => false,
        ]);

        Todo::create([
            'title' => 'Medium Priority Hidden',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
        ]);

        $response = $this->actingAs($user)->get(route('todos.index', [
            'priorities' => ['high', 'low'],
        ]));

        $response->assertOk();
        $response->assertSeeText('High Priority Visible');
        $response->assertSeeText('Low Priority Visible');
        $response->assertDontSeeText('Medium Priority Hidden');
    }

    public function test_checkbox_status_pending_excludes_overdue_incomplete_todos(): void
    {
        $user = User::factory()->create();

        Todo::create([
            'title' => 'Pending Visible',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
            'due_date' => today()->addDay(),
        ]);

        Todo::create([
            'title' => 'Overdue Hidden',
            'user_id' => $user->id,
            'priority' => 'medium',
            'completed' => false,
            'due_date' => today()->subDay(),
        ]);

        $response = $this->actingAs($user)->get(route('todos.index', [
            'statuses' => ['pending'],
        ]));

        $response->assertOk();
        $response->assertSeeText('Pending Visible');
        $response->assertDontSeeText('Overdue Hidden');
    }
}

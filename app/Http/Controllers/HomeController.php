<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function updateWeeklyGoal(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($user && $user->usertype === 'user', 403);

        $validated = $request->validate([
            'weekly_goal' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $user->update([
            'weekly_goal' => $validated['weekly_goal'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Weekly goal updated.',
                'weekly_goal' => $user->weekly_goal,
            ]);
        }

        return redirect()->route('home')->with('success', 'Weekly goal updated.');
    }

    public function index()
    {
        if (Auth::check()) {
            $usertype = Auth::user()->usertype;

            if ($usertype === 'user') {
                return view('dashboard', $this->getUserDashboardData());
            }

            if ($usertype === 'admin') {
                return view('admin.adminHome', $this->getAdminDashboardData());
            }

            return redirect()->back();
        }

        return redirect()->route('login');
    }

    // ── User Dashboard ────────────────────────────────────────────────────

    private function getUserDashboardData(): array
    {
        /** @var User $user */
        $user = Auth::user();

        $totalTodos     = $user->todos()->count();
        $completedTodos = $user->todos()->where('completed', true)->count();
        $pendingTodos   = $user->todos()->where('completed', false)->count();
        $overdueTodos   = $user->todos()
                               ->where('completed', false)
                               ->whereNotNull('due_date')
                               ->where('due_date', '<', today())
                               ->count();

        $completionPct = $totalTodos > 0
            ? (int) round(($completedTodos / $totalTodos) * 100)
            : 0;

        $trendData = $this->buildTrendData(
            Todo::where('user_id', $user->id)
        );

        $recentTodos = $user->todos()
                            ->withCount([
                                'subtasks',
                                'subtasks as completed_subtasks_count' => fn ($q) => $q->where('completed', true),
                            ])
                            ->latest()
                            ->limit(6)
                            ->get();

        $recentActivity = ActivityLog::forUser($user->id)
                                     ->latest()
                                     ->limit(6)
                                     ->get();

        $completedToday = $user->todos()
                               ->whereDate('completed_at', today())
                               ->count();

        $weeklyGoal = max(1, (int) ($user->weekly_goal ?? 10));
        $weeklyCompleted = $user->todos()
                                ->whereBetween('completed_at', [
                                    today()->copy()->startOfWeek(),
                                    today()->copy()->endOfWeek(),
                                ])
                                ->count();

        $weeklyGoalPct = $weeklyGoal > 0
            ? min(100, (int) round(($weeklyCompleted / $weeklyGoal) * 100))
            : 0;

        $dailyCompletions = $user->todos()
                                 ->selectRaw('DATE(completed_at) as day, COUNT(*) as total')
                                 ->whereNotNull('completed_at')
                                 ->where('completed_at', '>=', today()->copy()->subDays(60)->startOfDay())
                                 ->groupByRaw('DATE(completed_at)')
                                 ->pluck('total', 'day');

        $dailyStreak = 0;
        for ($i = 0; $i < 60; $i++) {
            $day = today()->copy()->subDays($i)->toDateString();
            if ((int) ($dailyCompletions[$day] ?? 0) > 0) {
                $dailyStreak++;
                continue;
            }

            break;
        }

        return compact(
            'totalTodos', 'completedTodos', 'pendingTodos', 'overdueTodos',
            'completionPct', 'trendData', 'recentTodos', 'recentActivity',
            'completedToday', 'dailyStreak', 'weeklyGoal', 'weeklyCompleted', 'weeklyGoalPct'
        );
    }

    // ── Admin Dashboard ───────────────────────────────────────────────────

    private function getAdminDashboardData(): array
    {
        $totalUsers     = User::count();
        $totalTodos     = Todo::count();
        $completedTodos = Todo::where('completed', true)->count();
        $pendingTodos   = Todo::where('completed', false)->count();
        $totalLogs      = ActivityLog::count();

        $systemCompletionPct = $totalTodos > 0
            ? (int) round(($completedTodos / $totalTodos) * 100)
            : 0;

        $trendData = $this->buildTrendData(Todo::query());

        $topUsers = User::withCount(['todos as todos_count' => fn ($q) => $q->where('completed', true)])
                        ->orderByDesc('todos_count')
                        ->limit(5)
                        ->get();

        $recentActivity = ActivityLog::latest()
                                     ->limit(10)
                                     ->get();

        return compact(
            'totalUsers', 'totalTodos', 'completedTodos', 'pendingTodos',
            'totalLogs', 'systemCompletionPct', 'trendData',
            'topUsers', 'recentActivity'
        );
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function buildTrendData(Builder $query): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => today()->subDays($i));

        $counts = (clone $query)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', today()->subDays(6)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->pluck('count', 'date');

        return $days->map(fn ($day) => [
            'label' => $day->format('M d'),
            'value' => (int) ($counts[$day->toDateString()] ?? 0),
        ])->values()->all();
    }
}

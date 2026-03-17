<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
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
                            ->latest()
                            ->limit(6)
                            ->get();

        $recentActivity = ActivityLog::forUser($user->id)
                                     ->latest()
                                     ->limit(6)
                                     ->get();

        return compact(
            'totalTodos', 'completedTodos', 'pendingTodos', 'overdueTodos',
            'completionPct', 'trendData', 'recentTodos', 'recentActivity'
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

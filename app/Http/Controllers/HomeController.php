<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Todo;
use App\Models\User;
use App\Services\ExperienceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct(
        private readonly ExperienceService $experienceService,
    ) {}

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

    public function index(Request $request)
    {
        if (Auth::check()) {
            $usertype = Auth::user()->usertype;

            if ($usertype === 'user') {
                return view('dashboard', $this->getUserDashboardData());
            }

            if ($usertype === 'admin') {
                return view('admin.adminHome', $this->getAdminDashboardData($request));
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

        // Keep rank/progress synchronized for legacy users without EXP updates yet.
        $experience = $this->experienceService->buildProgressForUser($user);
        $user->forceFill([
            'current_rank' => $experience['current_rank']['key'],
            'rank_progress_pct' => $experience['progress_pct'],
        ]);

        if ($user->isDirty(['current_rank', 'rank_progress_pct'])) {
            $user->save();
        }

        return compact(
            'totalTodos', 'completedTodos', 'pendingTodos', 'overdueTodos',
            'completionPct', 'trendData', 'recentTodos', 'recentActivity',
            'completedToday', 'dailyStreak', 'weeklyGoal', 'weeklyCompleted', 'weeklyGoalPct',
            'experience'
        );
    }

    // ── Admin Dashboard ───────────────────────────────────────────────────

    private function getAdminDashboardData(Request $request): array
    {
        $requestedGrowthRange = (string) $request->query('growth_range', 'daily');
        $growthRange = match ($requestedGrowthRange) {
            'today' => 'daily',
            'this_week' => 'weekly',
            'this_month' => 'monthly',
            default => $requestedGrowthRange,
        };

        if (!in_array($growthRange, ['daily', 'weekly', 'monthly'], true)) {
            $growthRange = 'daily';
        }

        $totalUsers = User::count();

        $activeUsers = User::whereIn(
            'id',
            ActivityLog::query()
                ->select('user_id')
                ->whereNotNull('user_id')
                ->where('action', 'login')
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct()
        )->count();

        $inactiveUsers = max(0, $totalUsers - $activeUsers);

        $roles = User::query()
            ->selectRaw('usertype, COUNT(*) as total')
            ->groupBy('usertype')
            ->pluck('total', 'usertype');

        $orderedRoles = collect(['admin', 'user'])
            ->map(fn (string $role) => [
                'label' => ucfirst($role),
                'value' => (int) ($roles[$role] ?? 0),
            ]);

        $customRoles = collect($roles)
            ->except(['admin', 'user'])
            ->map(fn ($total, $role) => [
                'label' => ucfirst((string) $role),
                'value' => (int) $total,
            ])
            ->values();

        $roleDistribution = $orderedRoles
            ->concat($customRoles)
            ->values()
            ->all();

        $userGrowthData = $this->buildUserGrowthData($growthRange);

        $recentActivity = ActivityLog::latest()
                                     ->limit(10)
                                     ->get();

        return compact(
            'totalUsers', 'activeUsers', 'inactiveUsers', 'growthRange',
            'roleDistribution', 'userGrowthData', 'recentActivity'
        );
    }

    private function buildUserGrowthData(string $range): array
    {
        return match ($range) {
            'weekly' => $this->buildWeeklyUserGrowthData(),
            'monthly' => $this->buildMonthlyUserGrowthData(),
            default => $this->buildDailyUserGrowthData(),
        };
    }

    private function buildDailyUserGrowthData(): array
    {
        $days = collect(range(13, 0))
            ->map(fn (int $i) => today()->copy()->subDays($i));

        $counts = User::query()
            ->selectRaw('DATE(created_at) as bucket, COUNT(*) as total')
            ->whereBetween('created_at', [
                $days->first()->copy()->startOfDay(),
                $days->last()->copy()->endOfDay(),
            ])
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'bucket');

        $counts = $this->normalizeGrowthBuckets($counts->all());

        return $days
            ->map(fn ($day) => [
                'label' => $day->format('M d'),
                'value' => (int) ($counts[$day->toDateString()] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function buildWeeklyUserGrowthData(): array
    {
        $weeks = collect(range(11, 0))
            ->map(fn (int $i) => now()->copy()->startOfWeek()->subWeeks($i));

        [$bucketSelect, $bucketGroup] = $this->resolveBucketSql('weekly');

        $counts = User::query()
            ->selectRaw("{$bucketSelect} as bucket, COUNT(*) as total")
            ->whereBetween('created_at', [
                $weeks->first()->copy()->startOfDay(),
                $weeks->last()->copy()->endOfWeek()->endOfDay(),
            ])
            ->groupByRaw($bucketGroup)
            ->pluck('total', 'bucket');

        $counts = $this->normalizeGrowthBuckets($counts->all());

        return $weeks
            ->map(fn ($start) => [
                'label' => $start->format('M d'),
                'value' => (int) ($counts[$start->toDateString()] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function buildMonthlyUserGrowthData(): array
    {
        $months = collect(range(11, 0))
            ->map(fn (int $i) => now()->copy()->startOfMonth()->subMonths($i));

        [$bucketSelect, $bucketGroup] = $this->resolveBucketSql('monthly');

        $counts = User::query()
            ->selectRaw("{$bucketSelect} as bucket, COUNT(*) as total")
            ->whereBetween('created_at', [
                $months->first()->copy()->startOfDay(),
                $months->last()->copy()->endOfMonth()->endOfDay(),
            ])
            ->groupByRaw($bucketGroup)
            ->pluck('total', 'bucket');

        $counts = $this->normalizeGrowthBuckets($counts->all());

        return $months
            ->map(fn ($start) => [
                'label' => $start->format('M Y'),
                'value' => (int) ($counts[$start->toDateString()] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function resolveBucketSql(string $range): array
    {
        $driver = DB::connection()->getDriverName();

        if ($range === 'weekly') {
            if ($driver === 'pgsql') {
                return [
                    "DATE_TRUNC('week', created_at)::date",
                    "DATE_TRUNC('week', created_at)::date",
                ];
            }

            return [
                'DATE_SUB(DATE(created_at), INTERVAL WEEKDAY(created_at) DAY)',
                'DATE_SUB(DATE(created_at), INTERVAL WEEKDAY(created_at) DAY)',
            ];
        }

        if ($range === 'monthly') {
            if ($driver === 'pgsql') {
                return [
                    "DATE_TRUNC('month', created_at)::date",
                    "DATE_TRUNC('month', created_at)::date",
                ];
            }

            return [
                "DATE_FORMAT(created_at, '%Y-%m-01')",
                "DATE_FORMAT(created_at, '%Y-%m-01')",
            ];
        }

        return ['DATE(created_at)', 'DATE(created_at)'];
    }

    private function normalizeGrowthBuckets(array $counts): array
    {
        return collect($counts)
            ->mapWithKeys(fn ($count, $bucket) => [
                substr((string) $bucket, 0, 10) => (int) $count,
            ])
            ->all();
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

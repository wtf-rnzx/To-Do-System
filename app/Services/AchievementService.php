<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;

class AchievementService
{
    public function syncDefinitions(): void
    {
        $definitions = config('achievements.definitions', []);

        $rows = collect($definitions)
            ->map(fn (array $item): array => [
                'slug' => $item['slug'],
                'title' => $item['title'],
                'description' => $item['description'],
                'condition_type' => $item['condition_type'],
                'threshold' => (int) $item['threshold'],
                'metric_key' => $item['metric_key'],
                'badge_icon' => $item['badge_icon'] ?? null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        Achievement::query()->upsert(
            $rows,
            ['slug'],
            ['title', 'description', 'condition_type', 'threshold', 'metric_key', 'badge_icon', 'is_active', 'updated_at']
        );
    }

    public function buildMetrics(User $user): array
    {
        $today = today();

        $completedTasks = $user->todos()->where('completed', true)->count();

        $dailyCompletions = $user->todos()
            ->selectRaw('DATE(completed_at) as day, COUNT(*) as total')
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $today->copy()->subDays(365)->startOfDay())
            ->groupByRaw('DATE(completed_at)')
            ->pluck('total', 'day');

        $dailyStreak = 0;
        for ($i = 0; $i < 365; $i++) {
            $day = $today->copy()->subDays($i)->toDateString();

            if ((int) ($dailyCompletions[$day] ?? 0) > 0) {
                $dailyStreak++;
                continue;
            }

            break;
        }

        $perfectDays = $user->todos()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total, SUM(CASE WHEN completed AND DATE(completed_at) = DATE(created_at) THEN 1 ELSE 0 END) as same_day_done')
            ->where('created_at', '>=', $today->copy()->subDays(365)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->get()
            ->filter(fn ($row) => (int) $row->total > 0 && (int) $row->same_day_done === (int) $row->total)
            ->count();

        $dueTrackedTodos = $user->todos()
            ->whereNotNull('due_date')
            ->get(['due_date', 'completed', 'completed_at']);

        $overdueFreeStreak = 0;
        for ($i = 0; $i < 60; $i++) {
            $day = $today->copy()->subDays($i)->toDateString();

            $hasDueCommitmentOnDay = $dueTrackedTodos->contains(
                fn ($todo): bool => $todo->due_date->toDateString() <= $day
            );

            // Do not count "clean" days before the user had any due-date obligation.
            if (! $hasDueCommitmentOnDay) {
                break;
            }

            $hasOverdueOnDay = $dueTrackedTodos->contains(function ($todo) use ($day): bool {
                if ($todo->due_date->toDateString() >= $day) {
                    return false;
                }

                if (! $todo->completed || is_null($todo->completed_at)) {
                    return true;
                }

                return $todo->completed_at->toDateString() > $day;
            });

            if ($hasOverdueOnDay) {
                break;
            }

            $overdueFreeStreak++;
        }

        return [
            'completed_tasks' => $completedTasks,
            'daily_streak' => $dailyStreak,
            'perfect_days' => $perfectDays,
            'overdue_free_streak' => $overdueFreeStreak,
            'recurring_tasks_created' => $user->todos()->whereNotNull('recurrence_type')->count(),
            'high_priority_completed' => $user->todos()->where('completed', true)->where('priority', 'high')->count(),
            'completed_subtasks' => $user->todos()->withCount(['subtasks as done_subtasks' => fn ($q) => $q->where('completed', true)])->get()->sum('done_subtasks'),
            'due_date_tasks_completed' => $user->todos()->where('completed', true)->whereNotNull('due_date')->count(),
        ];
    }

    public function evaluate(User $user): array
    {
        $this->syncDefinitions();

        $metrics = $this->buildMetrics($user);
        $achievements = Achievement::query()->where('is_active', true)->orderBy('id')->get();
        $newlyUnlocked = [];

        foreach ($achievements as $achievement) {
            $current = (int) ($metrics[$achievement->metric_key] ?? 0);
            $progress = min($current, (int) $achievement->threshold);

            /** @var UserAchievement $row */
            $row = UserAchievement::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'achievement_id' => $achievement->id,
                ],
                [
                    'progress' => $progress,
                    'is_visible' => true,
                ]
            );

            $wasUnlocked = ! is_null($row->unlocked_at);

            $row->progress = $progress;

            if (! $wasUnlocked && $current >= (int) $achievement->threshold) {
                $row->unlocked_at = now();
                $row->is_visible = true;
                $newlyUnlocked[] = [
                    'achievement' => $achievement,
                    'user_achievement' => $row,
                ];
            }

            $row->save();
        }

        foreach ($newlyUnlocked as $payload) {
            AchievementUnlocked::dispatch(
                $user,
                $payload['achievement'],
                $payload['user_achievement']
            );
        }

        return $newlyUnlocked;
    }

    public function getUserAchievementsForPage(User $user): array
    {
        $this->evaluate($user);

        $userAchievementMap = UserAchievement::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('achievement_id');

        $rows = Achievement::query()
            ->where('is_active', true)
            ->orderBy('threshold')
            ->orderBy('id')
            ->get()
            ->map(function (Achievement $achievement) use ($userAchievementMap): array {
                /** @var UserAchievement|null $pivot */
                $pivot = $userAchievementMap->get($achievement->id);

                $progress = (int) ($pivot?->progress ?? 0);
                $threshold = (int) $achievement->threshold;
                $unlocked = ! is_null($pivot?->unlocked_at);

                return [
                    'id' => $achievement->id,
                    'slug' => $achievement->slug,
                    'title' => $achievement->title,
                    'description' => $achievement->description,
                    'condition_type' => $achievement->condition_type,
                    'threshold' => $threshold,
                    'badge_icon' => $achievement->badge_icon,
                    'progress' => min($progress, $threshold),
                    'raw_progress' => $progress,
                    'progress_pct' => $threshold > 0 ? min(100, (int) round(($progress / $threshold) * 100)) : 0,
                    'unlocked' => $unlocked,
                    'unlocked_at' => $pivot?->unlocked_at,
                    'is_visible' => (bool) ($pivot?->is_visible ?? false),
                    'user_achievement_id' => $pivot?->id,
                ];
            })
            ->values()
            ->all();

        return $rows;
    }
}

<?php

namespace App\Services;

use App\Models\User;

class BadgeService
{
    public function buildMetrics(User $user): array
    {
        $completedTasks = $user->todos()->where('completed', true)->count();

        $dailyCompletions = $user->todos()
            ->selectRaw('DATE(completed_at) as day, COUNT(*) as total')
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', today()->copy()->subDays(365)->startOfDay())
            ->groupByRaw('DATE(completed_at)')
            ->pluck('total', 'day');

        $dailyStreak = 0;
        for ($i = 0; $i < 365; $i++) {
            $day = today()->copy()->subDays($i)->toDateString();

            if ((int) ($dailyCompletions[$day] ?? 0) > 0) {
                $dailyStreak++;
                continue;
            }

            break;
        }

        return [
            'completed_tasks' => $completedTasks,
            'daily_streak' => $dailyStreak,
        ];
    }

    public function awardEligibleBadges(User $user): array
    {
        $definitions = config('badges.definitions', []);
        $metrics = $this->buildMetrics($user);

        $alreadyEarned = $user->userBadges()
            ->pluck('badge_key')
            ->all();

        $newlyAwarded = [];

        foreach ($definitions as $definition) {
            $metric = $definition['metric'] ?? null;
            $target = (int) ($definition['target'] ?? 0);
            $badgeKey = $definition['key'] ?? null;

            if (! $metric || ! $target || ! $badgeKey) {
                continue;
            }

            $current = (int) ($metrics[$metric] ?? 0);

            if ($current < $target || in_array($badgeKey, $alreadyEarned, true)) {
                continue;
            }

            $badge = $user->userBadges()->create([
                'badge_key' => $badgeKey,
                'title' => $definition['title'] ?? $badgeKey,
                'description' => $definition['description'] ?? null,
                'icon' => $definition['icon'] ?? '🏅',
                'earned_at' => now(),
            ]);

            $alreadyEarned[] = $badgeKey;
            $newlyAwarded[] = $badge;
        }

        return $newlyAwarded;
    }

    public function getProgress(User $user, ?array $metrics = null): array
    {
        $definitions = config('badges.definitions', []);
        $metrics = $metrics ?? $this->buildMetrics($user);

        $earnedKeys = $user->userBadges()
            ->pluck('badge_key')
            ->all();

        return collect($definitions)
            ->map(function (array $definition) use ($metrics, $earnedKeys): array {
                $metric = $definition['metric'] ?? null;
                $target = max(1, (int) ($definition['target'] ?? 1));
                $current = (int) ($metrics[$metric] ?? 0);
                $earned = in_array($definition['key'] ?? '', $earnedKeys, true);

                return [
                    'key' => $definition['key'] ?? '',
                    'title' => $definition['title'] ?? 'Badge',
                    'description' => $definition['description'] ?? null,
                    'icon' => $definition['icon'] ?? '🏅',
                    'metric' => $metric,
                    'target' => $target,
                    'current' => min($current, $target),
                    'raw_current' => $current,
                    'earned' => $earned,
                    'progress_pct' => $earned ? 100 : min(100, (int) round(($current / $target) * 100)),
                ];
            })
            ->values()
            ->all();
    }
}

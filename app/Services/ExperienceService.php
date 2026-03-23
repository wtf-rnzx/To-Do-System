<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExperienceService
{
    public function calculateAward(Todo $todo): int
    {
        $basePoints = max(1, (int) config('experience.base_points', 10));
        $mode = (string) config('experience.mode', 'weighted');

        if ($mode === 'fixed') {
            return $basePoints;
        }

        $multipliers = config('experience.priority_multipliers', []);
        $priority = (string) ($todo->priority ?? 'medium');
        $multiplier = (float) ($multipliers[$priority] ?? ($multipliers['medium'] ?? 1.0));

        return max(1, (int) round($basePoints * $multiplier));
    }

    public function awardForCompletedTodo(Todo $todo): int
    {
        return DB::transaction(function () use ($todo): int {
            /** @var Todo $lockedTodo */
            $lockedTodo = Todo::query()->whereKey($todo->id)->lockForUpdate()->firstOrFail();

            if (! $lockedTodo->completed || ! is_null($lockedTodo->exp_awarded_at)) {
                return 0;
            }

            $points = $this->calculateAward($lockedTodo);

            /** @var User $user */
            $user = User::query()->whereKey($lockedTodo->user_id)->lockForUpdate()->firstOrFail();

            $user->total_exp = max(0, (int) $user->total_exp) + $points;

            $progress = $this->buildProgress($user->total_exp);
            $user->current_rank = $progress['current_rank']['key'];
            $user->rank_progress_pct = $progress['progress_pct'];
            $user->save();

            $lockedTodo->exp_awarded_at = now();
            $lockedTodo->save();

            return $points;
        });
    }

    public function buildProgressForUser(User $user): array
    {
        return $this->buildProgress((int) $user->total_exp);
    }

    public function buildProgress(int $totalExp): array
    {
        $totalExp = max(0, $totalExp);

        $ranks = $this->rankCollection();
        $rankCount = $ranks->count();

        $currentRank = $ranks
            ->filter(fn (array $rank): bool => (int) $rank['min_exp'] <= $totalExp)
            ->last() ?? $ranks->first();

        $currentIndex = $ranks->search(fn (array $rank): bool => $rank['key'] === $currentRank['key']);
        $nextRank = $currentIndex !== false ? $ranks->get($currentIndex + 1) : null;

        $currentMin = (int) $currentRank['min_exp'];

        if (is_null($nextRank)) {
            $progressPct = 100;
            $expIntoCurrent = max(0, $totalExp - $currentMin);
            $expSpan = null;
            $expToNext = 0;
        } else {
            $nextMin = (int) $nextRank['min_exp'];
            $expSpan = max(1, $nextMin - $currentMin);
            $expIntoCurrent = min($expSpan, max(0, $totalExp - $currentMin));
            $expToNext = max(0, $nextMin - $totalExp);
            $progressPct = min(100, (int) round(($expIntoCurrent / $expSpan) * 100));
        }

        $position = $currentIndex === false ? 1 : ((int) $currentIndex + 1);

        return [
            'total_exp' => $totalExp,
            'current_rank' => $currentRank,
            'next_rank' => $nextRank,
            'progress_pct' => $progressPct,
            'exp_into_current_rank' => $expIntoCurrent,
            'exp_to_next_rank' => $expToNext,
            'exp_span_current_to_next' => $expSpan,
            'is_max_rank' => is_null($nextRank),
            'all_ranks' => $ranks->values()->all(),
            'position' => $position,
            'rank_count' => $rankCount,
        ];
    }

    /**
     * @return Collection<int, array{key:string,name:string,min_exp:int,badge:?string}>
     */
    private function rankCollection(): Collection
    {
        $configured = collect(config('experience.ranks', []))
            ->map(function (array $rank): array {
                return [
                    'key' => (string) ($rank['key'] ?? 'novice'),
                    'name' => (string) ($rank['name'] ?? 'Novice'),
                    'min_exp' => max(0, (int) ($rank['min_exp'] ?? 0)),
                    'badge' => isset($rank['badge']) ? (string) $rank['badge'] : null,
                ];
            })
            ->sortBy('min_exp')
            ->values();

        if ($configured->isEmpty()) {
            return collect([
                [
                    'key' => 'novice',
                    'name' => 'Novice',
                    'min_exp' => 0,
                    'badge' => '🌱',
                ],
            ]);
        }

        if ((int) $configured->first()['min_exp'] !== 0) {
            $configured->prepend([
                'key' => 'novice',
                'name' => 'Novice',
                'min_exp' => 0,
                'badge' => '🌱',
            ]);
        }

        return $configured->values();
    }
}

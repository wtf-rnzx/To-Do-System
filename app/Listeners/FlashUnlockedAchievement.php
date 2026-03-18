<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;

class FlashUnlockedAchievement
{
    public function handle(AchievementUnlocked $event): void
    {
        if (! app()->bound('request')) {
            return;
        }

        $request = request();

        if (! $request->hasSession()) {
            return;
        }

        $existing = $request->session()->get('newly_unlocked_achievements', []);

        $existing[] = [
            'title' => $event->achievement->title,
            'description' => $event->achievement->description,
            'badge_icon' => $event->achievement->badge_icon,
            'unlocked_at' => optional($event->userAchievement->unlocked_at)->toIso8601String(),
        ];

        $request->session()->flash('newly_unlocked_achievements', $existing);
    }
}

<?php

namespace App\Events;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AchievementUnlocked
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Achievement $achievement,
        public readonly UserAchievement $userAchievement,
    ) {}
}

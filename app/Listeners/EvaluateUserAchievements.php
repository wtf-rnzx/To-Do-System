<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Services\AchievementService;

class EvaluateUserAchievements
{
    public function __construct(
        private readonly AchievementService $achievementService,
    ) {}

    public function handle(TaskCompleted $event): void
    {
        $this->achievementService->evaluate($event->user);
    }
}

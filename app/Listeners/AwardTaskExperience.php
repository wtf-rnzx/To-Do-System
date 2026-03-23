<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Services\ExperienceService;

class AwardTaskExperience
{
    public function __construct(
        private readonly ExperienceService $experienceService,
    ) {}

    public function handle(TaskCompleted $event): void
    {
        $this->experienceService->awardForCompletedTodo($event->todo);
    }
}

<?php

namespace App\Observers;

use App\Events\TaskCompleted;
use App\Models\Todo;
use App\Services\ActivityLogger;
use App\Services\RecurringTodoService;

class TodoObserver
{
    public function __construct(
        private readonly RecurringTodoService $recurringTodoService,
    ) {}

    public function created(Todo $todo): void
    {
        ActivityLogger::log(
            user:        auth()->user(),
            action:      'created',
            module:      'todos',
            description: "Created todo: '{$todo->title}'.",
            properties:  ['todo_id' => $todo->id, 'title' => $todo->title],
        );
    }

    public function updated(Todo $todo): void
    {
        $changes = $todo->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $changeKeys = array_values(array_diff(array_keys($changes), ['completed_at']));

        // Separate "toggle" (status-only change) from a real update
        $isToggle = $changeKeys === ['completed'];

        ActivityLogger::log(
            user:        auth()->user(),
            action:      $isToggle ? 'toggled' : 'updated',
            module:      'todos',
            description: $isToggle
                ? "Toggled todo '{$todo->title}' to " . ($todo->completed ? 'completed' : 'pending') . '.'
                : "Updated todo: '{$todo->title}'.",
            properties:  ['todo_id' => $todo->id, 'changes' => $changes],
        );

        if ($todo->wasChanged('completed') && $todo->completed) {
            if ($todo->user) {
                TaskCompleted::dispatch($todo->user, $todo);
            }

            if ($todo->recurrence_type) {
                $this->recurringTodoService->generateNextInstance($todo);
            }
        }
    }

    public function deleted(Todo $todo): void
    {
        ActivityLogger::log(
            user:        auth()->user(),
            action:      'deleted',
            module:      'todos',
            description: "Deleted todo: '{$todo->title}'.",
            properties:  ['todo_id' => $todo->id, 'title' => $todo->title],
        );
    }
}

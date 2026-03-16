<?php

namespace App\Observers;

use App\Models\Todo;
use App\Services\ActivityLogger;

class TodoObserver
{
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

        // Separate "toggle" (status-only change) from a real update
        $isToggle = array_keys($changes) === ['completed'] ||
                    array_keys($changes) === ['completed', 'updated_at'];

        ActivityLogger::log(
            user:        auth()->user(),
            action:      $isToggle ? 'toggled' : 'updated',
            module:      'todos',
            description: $isToggle
                ? "Toggled todo '{$todo->title}' to " . ($todo->completed ? 'completed' : 'pending') . '.'
                : "Updated todo: '{$todo->title}'.",
            properties:  ['todo_id' => $todo->id, 'changes' => $changes],
        );
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

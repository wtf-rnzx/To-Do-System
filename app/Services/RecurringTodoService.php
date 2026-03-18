<?php

namespace App\Services;

use App\Models\Todo;
use Carbon\Carbon;

class RecurringTodoService
{
    public function generateNextInstance(Todo $todo): ?Todo
    {
        if (! in_array($todo->recurrence_type, ['daily', 'weekly', 'monthly'], true)) {
            return null;
        }

        $originId = $todo->recurrence_origin_id ?: $todo->id;

        $baseDate = $todo->due_date
            ? Carbon::parse($todo->due_date)
            : today();

        $nextDueDate = match ($todo->recurrence_type) {
            'daily' => $baseDate->copy()->addDay(),
            'weekly' => $baseDate->copy()->addWeek(),
            'monthly' => $baseDate->copy()->addMonthNoOverflow(),
            default => null,
        };

        if (! $nextDueDate) {
            return null;
        }

        return Todo::firstOrCreate(
            [
                'recurrence_origin_id' => $originId,
                'due_date' => $nextDueDate->toDateString(),
            ],
            [
                'user_id' => $todo->user_id,
                'title' => $todo->title,
                'description' => $todo->description,
                'priority' => $todo->priority,
                'recurrence_type' => $todo->recurrence_type,
                'completed' => false,
            ]
        );
    }
}

<?php

return [
    'definitions' => [
        [
            'key' => 'first_task_completed',
            'title' => 'First Win',
            'description' => 'Complete your first task.',
            'metric' => 'completed_tasks',
            'target' => 1,
            'icon' => '🎯',
        ],
        [
            'key' => 'streak_7_days',
            'title' => '7-Day Streak',
            'description' => 'Complete at least one task for 7 days in a row.',
            'metric' => 'daily_streak',
            'target' => 7,
            'icon' => '🔥',
        ],
        [
            'key' => 'tasks_10_completed',
            'title' => 'Task Finisher 10',
            'description' => 'Complete 10 tasks.',
            'metric' => 'completed_tasks',
            'target' => 10,
            'icon' => '🥉',
        ],
        [
            'key' => 'tasks_50_completed',
            'title' => 'Task Finisher 50',
            'description' => 'Complete 50 tasks.',
            'metric' => 'completed_tasks',
            'target' => 50,
            'icon' => '🥈',
        ],
        [
            'key' => 'tasks_100_completed',
            'title' => 'Task Finisher 100',
            'description' => 'Complete 100 tasks.',
            'metric' => 'completed_tasks',
            'target' => 100,
            'icon' => '🥇',
        ],
    ],
];

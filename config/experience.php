<?php

return [
    // EXP awarding strategy: fixed or weighted by task priority.
    'mode' => env('EXP_MODE', 'fixed'), // fixed|weighted

    // Used in fixed mode, and as base value in weighted mode.
    'base_points' => (int) env('EXP_BASE_POINTS', 10),

    // Priority multipliers for weighted mode.
    'priority_multipliers' => [
        'low' => 1.0,
        'medium' => 1.5,
        'high' => 2.0,
    ],

    // Ordered ranks by min_exp ascending.
    'ranks' => [
        [
            'key' => 'novice',
            'name' => 'Novice',
            'min_exp' => 0,
            'badge' => '🌱',
        ],
        [
            'key' => 'apprentice',
            'name' => 'Apprentice',
            'min_exp' => 150,
            'badge' => '🧭',
        ],
        [
            'key' => 'executor',
            'name' => 'Executor',
            'min_exp' => 400,
            'badge' => '⚡',
        ],
        [
            'key' => 'strategist',
            'name' => 'Strategist',
            'min_exp' => 800,
            'badge' => '🛡️',
        ],
        [
            'key' => 'master',
            'name' => 'Master',
            'min_exp' => 1400,
            'badge' => '🏆',
        ],
        [
            'key' => 'legend',
            'name' => 'Legend',
            'min_exp' => 2200,
            'badge' => '👑',
        ],
    ],
];

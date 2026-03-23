<?php

return [
    // EXP awarding strategy.
    // - priority: uses explicit per-priority points from priority_points.
    // - weighted: uses base_points * priority_multipliers.
    // - fixed: uses base_points only.
    'mode' => env('EXP_MODE', 'priority'), // priority|fixed|weighted

    // Used in fixed mode, and as base value in weighted mode.
    'base_points' => (int) env('EXP_BASE_POINTS', 10),

    // Priority multipliers for weighted mode.
    'priority_multipliers' => [
        'low' => 1.0,
        'medium' => 1.5,
        'high' => 2.0,
    ],

    // Explicit per-priority EXP awards used in priority mode.
    'priority_points' => [
        'low' => (int) env('EXP_LOW_POINTS', 10),
        'medium' => (int) env('EXP_MEDIUM_POINTS', 15),
        'high' => (int) env('EXP_HIGH_POINTS', 20),
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

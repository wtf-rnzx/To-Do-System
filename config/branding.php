<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Brand Assets
    |--------------------------------------------------------------------------
    |
    | Centralized branding assets. Keep theme-to-logo mapping here so logo
    | paths are defined in one place and can scale to future themes.
    |
    */
    'logo' => [
        'themes' => [
            'light' => 'images/StagStack-lightTheme.png',
            'dark' => 'images/StagStack-darkTheme.png',
        ],

        // Fallback used when theme detection or asset loading fails.
        'fallback' => 'images/StagStack-lightTheme.png',

        // Future-proof placeholder for additional theme mappings.
        // Example: 'high-contrast' => 'images/StagStack-highContrast.png',
    ],
];

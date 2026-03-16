<?php

namespace App\Services;

use App\Jobs\LogActivityJob;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Thin facade around the async logging pipeline.
 *
 * Call ActivityLogger::log(...) anywhere in the app — the work is handed off
 * to a queued job so the current request is never blocked.
 */
class ActivityLogger
{
    public static function log(
        ?User $user,
        string $action,
        string $module,
        string $description,
        ?Request $request = null,
        array $properties = [],
    ): void {
        $req = $request ?? request();

        dispatch(new LogActivityJob(
            userId:      $user?->id,
            userName:    $user?->name ?? 'System',
            action:      $action,
            module:      $module,
            description: $description,
            ipAddress:   $req?->ip(),
            userAgent:   $req?->userAgent(),
            properties:  $properties,
        ));
    }
}

<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        ActivityLogger::log(
            user:        $event->user,
            action:      'logout',
            module:      'auth',
            description: "User '{$event->user->name}' logged out.",
        );
    }
}

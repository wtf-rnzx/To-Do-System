<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        ActivityLogger::log(
            user:        $event->user,
            action:      'login',
            module:      'auth',
            description: "User '{$event->user->name}' logged in.",
        );
    }
}

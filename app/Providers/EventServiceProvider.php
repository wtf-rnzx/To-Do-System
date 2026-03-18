<?php

namespace App\Providers;

use App\Listeners\LogUserLogin;
use App\Listeners\LogUserLogout;
use App\Listeners\EvaluateUserAchievements;
use App\Listeners\FlashUnlockedAchievement;
use App\Events\TaskCompleted;
use App\Events\AchievementUnlocked;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            LogUserLogin::class,
        ],
        Logout::class => [
            LogUserLogout::class,
        ],
        TaskCompleted::class => [
            EvaluateUserAchievements::class,
        ],
        AchievementUnlocked::class => [
            FlashUnlockedAchievement::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

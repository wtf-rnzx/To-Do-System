<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Persists a single activity log entry asynchronously.
 *
 * By implementing ShouldQueue the job is dispatched to the queue driver
 * configured in .env (QUEUE_CONNECTION). In development this defaults to
 * "sync" (runs immediately, in-process). In production set it to "database"
 * or "redis" and run `php artisan queue:work`.
 */
class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        public readonly ?int    $userId,
        public readonly string  $userName,
        public readonly string  $action,
        public readonly string  $module,
        public readonly string  $description,
        public readonly ?string $ipAddress,
        public readonly ?string $userAgent,
        public readonly array   $properties,
    ) {}

    public function handle(): void
    {
        ActivityLog::create([
            'user_id'     => $this->userId,
            'user_name'   => $this->userName,
            'action'      => $this->action,
            'module'      => $this->module,
            'description' => $this->description,
            'ip_address'  => $this->ipAddress,
            'user_agent'  => $this->userAgent,
            'properties'  => $this->properties ?: null,
        ]);
    }
}

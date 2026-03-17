<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    // Logs are append-only — no updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('user_name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('ip_address', 'like', "%{$term}%");
        });
    }

    public function scopeDateFrom(Builder $query, string $date): Builder
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    public function scopeDateTo(Builder $query, string $date): Builder
    {
        return $query->whereDate('created_at', '<=', $date);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Human-readable badge colour for action types. */
    public function actionBadgeClass(): string
    {
        return match ($this->action) {
            'login'           => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            'logout'          => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
            'created'         => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            'updated'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            'deleted'         => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            'toggled'         => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
            'role_updated'    => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
            'profile_updated' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300',
            'account_deleted' => 'bg-red-200 text-red-800 dark:bg-red-800/40 dark:text-red-200',
            default           => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        };
    }

    /** Available actions for the filter dropdown. */
    public static function availableActions(): array
    {
        return [
            'login', 'logout', 'created', 'updated',
            'deleted', 'toggled', 'role_updated', 'profile_updated', 'account_deleted',
        ];
    }

    /** Available modules for the filter dropdown. */
    public static function availableModules(): array
    {
        return ['auth', 'todos', 'users', 'profile'];
    }
}

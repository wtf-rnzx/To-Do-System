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
            // Success / positive actions
            'login', 'created', 'inserted', 'insert' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200',

            // Neutral informational actions
            'logout', 'toggled' => 'bg-slate-100 text-slate-800 dark:bg-slate-700/70 dark:text-slate-200',

            // Update / warning actions
            'updated', 'role_updated', 'profile_updated', 'update' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200',

            // Danger actions
            'deleted', 'account_deleted', 'delete' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/65 dark:text-rose-200',

            // Fallback informational style
            default => 'bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-200',
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

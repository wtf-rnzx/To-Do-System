<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'completed',
        'due_date',
        'completed_at',
        'priority',
        'recurrence_type',
        'recurrence_origin_id',
        'user_id',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $todo): void {
            if ($todo->isDirty('completed')) {
                $todo->completed_at = $todo->completed ? now() : null;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(TodoSubtask::class)->orderBy('position')->orderBy('id');
    }

    public function snoozeHistory(): HasMany
    {
        return $this->hasMany(TodoSnoozeHistory::class)->latest('snoozed_at');
    }

    public function recurrenceOrigin(): BelongsTo
    {
        return $this->belongsTo(self::class, 'recurrence_origin_id');
    }

    public function recurrenceInstances(): HasMany
    {
        return $this->hasMany(self::class, 'recurrence_origin_id');
    }
}

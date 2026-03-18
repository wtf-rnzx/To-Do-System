<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodoSubtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'todo_id',
        'title',
        'completed',
        'position',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $subtask): void {
            if ($subtask->isDirty('completed')) {
                $subtask->completed_at = $subtask->completed ? now() : null;
            }
        });
    }

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }
}

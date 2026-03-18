<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodoSnoozeHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'todo_id',
        'user_id',
        'old_due_date',
        'new_due_date',
        'reason',
        'snoozed_at',
    ];

    protected $casts = [
        'old_due_date' => 'date',
        'new_due_date' => 'date',
        'snoozed_at' => 'datetime',
    ];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

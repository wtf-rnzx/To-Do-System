<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'completed',
        'due_date',
        'user_id',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

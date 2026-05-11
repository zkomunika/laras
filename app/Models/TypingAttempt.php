<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypingAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'level_id',
        'typed_text',
        'correct_chars',
        'wrong_chars',
        'mistakes',
        'accuracy',
        'wpm',
        'score',
        'stars',
        'completed',
        'duration_ms',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'accuracy' => 'float',
        'wpm' => 'float',
        'completed' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
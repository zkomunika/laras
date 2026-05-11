<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerProgress extends Model
{
    protected $table = 'player_progress';

    protected $fillable = [
        'user_id',
        'level_id',
        'best_wpm',
        'best_accuracy',
        'best_score',
        'best_stars',
        'attempts_count',
        'is_completed',
        'unlocked_at',
        'completed_at',
    ];

    protected $casts = [
        'best_wpm' => 'float',
        'best_accuracy' => 'float',
        'is_completed' => 'boolean',
        'unlocked_at' => 'datetime',
        'completed_at' => 'datetime',
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
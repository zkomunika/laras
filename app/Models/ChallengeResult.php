<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeResult extends Model
{
    protected $fillable = [
        'challenge_room_id',
        'user_id',
        'level_id',
        'typed_text',
        'wpm',
        'accuracy',
        'mistakes',
        'score',
        'duration_ms',
        'rank',
        'completed',
        'failed_rules',
    ];

    protected $casts = [
        'wpm' => 'float',
        'accuracy' => 'float',
        'completed' => 'boolean',
        'failed_rules' => 'array',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(ChallengeRoom::class, 'challenge_room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}

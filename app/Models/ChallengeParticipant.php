<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeParticipant extends Model
{
    public const STATUS_WAITING = 'waiting';
    public const STATUS_PLAYING = 'playing';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_LEFT = 'left';

    protected $fillable = [
        'challenge_room_id',
        'user_id',
        'is_ready',
        'progress_percent',
        'typed_chars',
        'mistakes',
        'wpm',
        'accuracy',
        'status',
        'finished_at',
    ];

    protected $casts = [
        'is_ready' => 'boolean',
        'progress_percent' => 'float',
        'wpm' => 'float',
        'accuracy' => 'float',
        'finished_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(ChallengeRoom::class, 'challenge_room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

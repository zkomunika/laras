<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChallengeRoom extends Model
{
    public const TYPE_PUBLIC = 'public';
    public const TYPE_PRIVATE = 'private';

    public const STATUS_WAITING = 'waiting';
    public const STATUS_PLAYING = 'playing';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'master_user_id',
        'level_id',
        'name',
        'type',
        'code',
        'capacity',
        'word_count',
        'time_limit_seconds',
        'target_text',
        'status',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'word_count' => 'integer',
        'time_limit_seconds' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'master_user_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChallengeParticipant::class);
    }

    public function activeParticipants(): HasMany
    {
        return $this->hasMany(ChallengeParticipant::class)->where('status', '!=', 'left');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ChallengeResult::class);
    }

    public function isWaiting(): bool
    {
        return $this->status === self::STATUS_WAITING;
    }

    public function isPrivate(): bool
    {
        return $this->type === self::TYPE_PRIVATE;
    }
}

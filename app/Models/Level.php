<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    protected $fillable = [
        'chapter_id',
        'level_number',
        'title',
        'story_text',
        'target_text',
        'target_wpm',
        'min_accuracy',
        'time_limit_seconds',
        'max_mistakes',
        'is_boss_level',
        'sort_order',
    ];

    protected $casts = [
        'min_accuracy' => 'float',
        'is_boss_level' => 'boolean',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TypingAttempt::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(PlayerProgress::class);
    }
}
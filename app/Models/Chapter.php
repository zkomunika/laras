<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $fillable = [
        'number',
        'title',
        'slug',
        'description',
        'theme',
        'start_level',
        'end_level',
        'is_active',
    ];

    public function levels(): HasMany
    {
        return $this->hasMany(Level::class)->orderBy('level_number');
    }
}
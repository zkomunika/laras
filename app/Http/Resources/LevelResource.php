<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chapter_id' => $this->chapter_id,
            'level_number' => $this->level_number,
            'title' => $this->title,
            'story_text' => $this->story_text,
            'target_text' => $this->target_text,
            'target_wpm' => $this->target_wpm,
            'min_accuracy' => $this->min_accuracy,
            'time_limit_seconds' => $this->time_limit_seconds,
            'max_mistakes' => $this->max_mistakes,
            'is_boss_level' => $this->is_boss_level,
            'sort_order' => $this->sort_order,
            'chapter' => new ChapterResource($this->whenLoaded('chapter')),
        ];
    }
}
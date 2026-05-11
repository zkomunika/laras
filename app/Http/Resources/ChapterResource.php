<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'theme' => $this->theme,
            'start_level' => $this->start_level,
            'end_level' => $this->end_level,
            'levels_count' => $this->whenCounted('levels'),
            'levels' => LevelResource::collection($this->whenLoaded('levels')),
        ];
    }
}
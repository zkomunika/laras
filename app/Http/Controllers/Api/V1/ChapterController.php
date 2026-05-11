<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChapterResource;
use App\Http\Resources\LevelResource;
use App\Models\Chapter;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::query()
            ->where('is_active', true)
            ->withCount('levels')
            ->orderBy('number')
            ->get();

        return ChapterResource::collection($chapters);
    }

    public function show(Chapter $chapter)
    {
        $chapter->load([
            'levels' => fn ($query) => $query->orderBy('level_number'),
        ]);

        return new ChapterResource($chapter);
    }

    public function levels(Chapter $chapter)
    {
        $levels = $chapter->levels()
            ->orderBy('level_number')
            ->get();

        return LevelResource::collection($levels);
    }
}
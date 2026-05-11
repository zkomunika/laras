<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LevelResource;
use App\Models\Level;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::query()
            ->with('chapter')
            ->orderBy('level_number')
            ->get();

        return LevelResource::collection($levels);
    }

    public function show(Level $level)
    {
        $level->load('chapter');

        return new LevelResource($level);
    }
}
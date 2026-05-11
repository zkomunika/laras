<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PlayerProgress;

class ProgressController extends Controller
{
    public function index()
    {
        $progress = PlayerProgress::query()
            ->with('level')
            ->where('user_id', 1)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'level_id' => $item->level_id,
                    'level_number' => $item->level?->level_number,
                    'best_wpm' => $item->best_wpm,
                    'best_accuracy' => $item->best_accuracy,
                    'best_score' => $item->best_score,
                    'best_stars' => $item->best_stars,
                    'attempts_count' => $item->attempts_count,
                    'is_completed' => $item->is_completed,
                    'unlocked_at' => $item->unlocked_at,
                    'completed_at' => $item->completed_at,
                ];
            });

        return response()->json([
            'data' => $progress,
        ]);
    }
}
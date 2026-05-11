<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PlayerProgress;

class LeaderboardController extends Controller
{
    public function index()
    {
        $leaderboard = PlayerProgress::query()
            ->selectRaw('
                user_id,
                SUM(best_score) as total_score,
                COUNT(CASE WHEN is_completed = 1 THEN 1 END) as completed_levels,
                MAX(best_wpm) as best_wpm,
                ROUND(AVG(NULLIF(best_accuracy, 0)), 2) as average_accuracy
            ')
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->limit(20)
            ->get()
            ->values()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'user_id' => $item->user_id,
                    'name' => $item->user?->name ?? 'Unknown Player',
                    'email' => $item->user?->email,
                    'total_score' => (int) $item->total_score,
                    'completed_levels' => (int) $item->completed_levels,
                    'best_wpm' => (float) $item->best_wpm,
                    'average_accuracy' => (float) ($item->average_accuracy ?? 0),
                ];
            });

        return response()->json([
            'data' => $leaderboard,
        ]);
    }
}
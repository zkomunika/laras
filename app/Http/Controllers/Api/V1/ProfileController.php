<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PlayerProgress;
use App\Models\TypingAttempt;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $user = User::findOrFail(1);

        $progress = PlayerProgress::query()
            ->where('user_id', $user->id)
            ->get();

        $attemptsCount = TypingAttempt::query()
            ->where('user_id', $user->id)
            ->count();

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'stats' => [
                    'completed_levels' => $progress->where('is_completed', true)->count(),
                    'unlocked_levels' => $progress->whereNotNull('unlocked_at')->count(),
                    'total_attempts' => $attemptsCount,
                    'total_score' => $progress->sum('best_score'),
                    'best_wpm' => round($progress->max('best_wpm') ?? 0, 2),
                    'average_accuracy' => round($progress->where('best_accuracy', '>', 0)->avg('best_accuracy') ?? 0, 2),
                ],
            ],
        ]);
    }
}
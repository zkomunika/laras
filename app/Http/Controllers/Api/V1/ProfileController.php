<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GameNotification;
use App\Models\PlayerProgress;
use App\Models\TypingAttempt;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request, AchievementService $achievementService)
    {
        $user = $request->user();

        $progress = PlayerProgress::query()
            ->where('user_id', $user->id)
            ->get();

        $attemptsCount = TypingAttempt::query()
            ->where('user_id', $user->id)
            ->count();

        $achievements = $achievementService->achievementListForUser($user);

        $recentNotifications = GameNotification::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (GameNotification $notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ]);

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
                    'total_stars' => $progress->sum('best_stars'),
                    'best_wpm' => round($progress->max('best_wpm') ?? 0, 2),
                    'average_accuracy' => round($progress->where('best_accuracy', '>', 0)->avg('best_accuracy') ?? 0, 2),
                    'achievement_unlocked' => $achievements->where('unlocked', true)->count(),
                    'achievement_total' => $achievements->count(),
                    'unread_notifications' => GameNotification::query()
                        ->where('user_id', $user->id)
                        ->whereNull('read_at')
                        ->count(),
                ],
                'achievements' => $achievements,
                'recent_notifications' => $recentNotifications,
            ],
        ]);
    }
}

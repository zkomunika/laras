<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\GameNotification;
use App\Models\PlayerProgress;
use App\Models\TypingAttempt;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Collection;

class AchievementService
{
    public function syncForUser(User $user): array
    {
        $stats = $this->buildStats($user);
        $newAchievements = [];

        Achievement::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function (Achievement $achievement) use ($user, $stats, &$newAchievements) {
                if (!$this->isUnlockedByStats($achievement, $stats)) {
                    return;
                }

                $userAchievement = UserAchievement::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'achievement_id' => $achievement->id,
                    ],
                    [
                        'unlocked_at' => now(),
                    ]
                );

                if (!$userAchievement->wasRecentlyCreated) {
                    return;
                }

                $newAchievements[] = $achievement;

                GameNotification::create([
                    'user_id' => $user->id,
                    'title' => 'Achievement terbuka',
                    'message' => $achievement->icon . ' ' . $achievement->name . ' berhasil kamu dapatkan.',
                    'type' => 'achievement',
                    'data' => [
                        'achievement_id' => $achievement->id,
                        'achievement_code' => $achievement->code,
                    ],
                ]);
            });

        return $newAchievements;
    }

    public function achievementListForUser(User $user): Collection
    {
        $this->syncForUser($user);

        $unlocked = UserAchievement::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('achievement_id');

        return Achievement::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (Achievement $achievement) use ($unlocked) {
                $userAchievement = $unlocked->get($achievement->id);

                return [
                    'id' => $achievement->id,
                    'code' => $achievement->code,
                    'name' => $achievement->name,
                    'description' => $achievement->description,
                    'icon' => $achievement->icon,
                    'condition_type' => $achievement->condition_type,
                    'condition_value' => $achievement->condition_value,
                    'unlocked' => $userAchievement !== null,
                    'unlocked_at' => $userAchievement?->unlocked_at,
                ];
            });
    }

    private function buildStats(User $user): array
    {
        $progress = PlayerProgress::query()
            ->with('level.chapter')
            ->where('user_id', $user->id)
            ->get();

        $completedProgress = $progress->where('is_completed', true);

        $attempts = TypingAttempt::query()
            ->where('user_id', $user->id)
            ->get();

        $completedAttempts = $attempts->where('completed', true);

        $completedChapterIds = $completedProgress
            ->groupBy(fn (PlayerProgress $item) => $item->level?->chapter_id)
            ->filter(fn (Collection $items, $chapterId) => $chapterId && $items->count() >= 10)
            ->keys()
            ->map(fn ($chapterId) => (int) $chapterId)
            ->values()
            ->all();

        return [
            'account_created' => true,
            'completed_levels' => $completedProgress->count(),
            'completed_level_numbers' => $completedProgress
                ->map(fn (PlayerProgress $item) => (int) $item->level?->level_number)
                ->filter()
                ->values()
                ->all(),
            'completed_chapter_ids' => $completedChapterIds,
            'zero_mistake_levels' => $completedAttempts->where('mistakes', 0)->pluck('level_id')->unique()->count(),
            'best_wpm' => (float) ($progress->max('best_wpm') ?? 0),
            'total_score' => (int) $progress->sum('best_score'),
            'total_stars' => (int) $progress->sum('best_stars'),
            'total_attempts' => $attempts->count(),
        ];
    }

    private function isUnlockedByStats(Achievement $achievement, array $stats): bool
    {
        $value = (int) $achievement->condition_value;

        return match ($achievement->condition_type) {
            'account_created' => true,
            'completed_levels' => $stats['completed_levels'] >= $value,
            'completed_chapter' => in_array($value, $stats['completed_chapter_ids'], true),
            'zero_mistake_levels' => $stats['zero_mistake_levels'] >= $value,
            'best_wpm' => $stats['best_wpm'] >= $value,
            'total_score' => $stats['total_score'] >= $value,
            'total_stars' => $stats['total_stars'] >= $value,
            'level_completed' => in_array($value, $stats['completed_level_numbers'], true),
            'total_attempts' => $stats['total_attempts'] >= $value,
            default => false,
        };
    }
}

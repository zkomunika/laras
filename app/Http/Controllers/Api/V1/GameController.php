<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\PlayerProgress;
use App\Models\TypingAttempt;
use App\Services\AchievementService;
use App\Services\TypingGameService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GameController extends Controller
{
    public function start(Request $request)
    {
        $validated = $request->validate([
            'level_id' => ['required', 'exists:levels,id'],
        ]);

        $level = Level::findOrFail($validated['level_id']);

        if (!$this->isLevelUnlocked($request->user()->id, $level)) {
            throw ValidationException::withMessages([
                'level_id' => 'Level ini masih terkunci. Selesaikan level sebelumnya dulu.',
            ]);
        }

        $attempt = TypingAttempt::create([
            'user_id' => $request->user()->id,
            'level_id' => $level->id,
            'started_at' => now(),
        ]);

        return response()->json([
            'message' => 'Game started.',
            'data' => [
                'id' => $attempt->id,
                'level_id' => $attempt->level_id,
                'level_number' => $level->level_number,
                'target_wpm' => $level->target_wpm,
                'min_accuracy' => $level->min_accuracy,
                'time_limit_seconds' => $level->time_limit_seconds,
                'max_mistakes' => $level->max_mistakes,
                'started_at' => $attempt->started_at,
            ],
        ]);
    }

    public function submit(Request $request, TypingGameService $typingGameService, AchievementService $achievementService)
    {
        $validated = $request->validate([
            'attempt_id' => ['required', 'exists:typing_attempts,id'],
            'typed_text' => ['nullable', 'string'],
        ]);

        $attempt = TypingAttempt::with('level')
            ->where('user_id', $request->user()->id)
            ->findOrFail($validated['attempt_id']);

        if ($attempt->finished_at !== null) {
            throw ValidationException::withMessages([
                'attempt_id' => 'Attempt ini sudah selesai dan tidak bisa dikirim ulang.',
            ]);
        }

        $level = $attempt->level;

        $result = $typingGameService->calculate(
            $level,
            $attempt,
            $validated['typed_text'] ?? ''
        );

        $meta = $result['meta'];
        unset($result['meta']);

        $attempt->update($result);

        $attempt->refresh();
        $attempt->load('level');

        $typingGameService->updateProgress($attempt);
        $newAchievements = collect($achievementService->syncForUser($request->user()))
            ->map(fn ($achievement) => [
                'id' => $achievement->id,
                'code' => $achievement->code,
                'name' => $achievement->name,
                'description' => $achievement->description,
                'icon' => $achievement->icon,
            ])
            ->values();

        return response()->json([
            'message' => 'Game result saved',
            'data' => [
                'attempt_id' => $attempt->id,
                'level_id' => $attempt->level_id,
                'level_number' => $level->level_number,
                'wpm' => $attempt->wpm,
                'accuracy' => $attempt->accuracy,
                'mistakes' => $attempt->mistakes,
                'score' => $attempt->score,
                'stars' => $attempt->stars,
                'completed' => $attempt->completed,
                'duration_ms' => $attempt->duration_ms,
                'duration_seconds' => round($attempt->duration_ms / 1000, 2),
                'passed_requirements' => $meta['passed_requirements'],
                'failed_rules' => $meta['failed_rules'],
                'thresholds' => [
                    'target_wpm' => $meta['target_wpm'],
                    'min_accuracy' => $meta['min_accuracy'],
                    'time_limit_seconds' => $meta['time_limit_seconds'],
                    'max_mistakes' => $meta['max_mistakes'],
                ],
                'new_achievements' => $newAchievements,
            ],
        ]);
    }

    private function isLevelUnlocked(int $userId, Level $level): bool
    {
        if ((int) $level->level_number === 1) {
            return true;
        }

        return PlayerProgress::where('user_id', $userId)
            ->where('level_id', $level->id)
            ->where(function ($query) {
                $query->whereNotNull('unlocked_at')
                    ->orWhere('is_completed', true);
            })
            ->exists();
    }
}

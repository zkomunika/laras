<?php

namespace App\Services;

use App\Models\Level;
use App\Models\PlayerProgress;
use App\Models\TypingAttempt;

class TypingGameService
{
    public function calculate(Level $level, TypingAttempt $attempt, string $typedText): array
    {
        $targetText = $level->target_text;

        $typedLength = mb_strlen($typedText);
        $targetLength = mb_strlen($targetText);

        $correctChars = 0;
        $wrongChars = 0;

        for ($i = 0; $i < $typedLength; $i++) {
            $typedChar = mb_substr($typedText, $i, 1);
            $targetChar = $i < $targetLength ? mb_substr($targetText, $i, 1) : '';

            if ($typedChar === $targetChar) {
                $correctChars++;
            } else {
                $wrongChars++;
            }
        }

        $finishedAt = now();
        $startedAt = $attempt->started_at ?? now();

        $durationMs = max(1000, $finishedAt->diffInMilliseconds($startedAt));
        $durationMinutes = $durationMs / 60000;

        $accuracy = $typedLength > 0
            ? round(($correctChars / $typedLength) * 100, 2)
            : 0;

        $wpm = $durationMinutes > 0
            ? round(($correctChars / 5) / $durationMinutes, 2)
            : 0;

        $mistakes = $wrongChars;
        $completed = $typedText === $targetText;

        $wpmScore = ($wpm / max(1, $level->target_wpm)) * 50;
        $accScore = ($accuracy / 100) * 50;
        
        $score = max(
            0,
            (int) round($wpmScore + $accScore - ($mistakes * 2))
        );

        $stars = $this->calculateStars($completed, $wpm, $accuracy, $level);

        return [
            'typed_text' => $typedText,
            'correct_chars' => $correctChars,
            'wrong_chars' => $wrongChars,
            'mistakes' => $mistakes,
            'accuracy' => $accuracy,
            'wpm' => $wpm,
            'score' => $score,
            'stars' => $stars,
            'completed' => $completed,
            'duration_ms' => $durationMs,
            'finished_at' => $finishedAt,
        ];
    }

    public function updateProgress(TypingAttempt $attempt): PlayerProgress
    {
        $progress = PlayerProgress::firstOrNew([
            'user_id' => $attempt->user_id,
            'level_id' => $attempt->level_id,
        ]);

        if (!$progress->unlocked_at) {
            $progress->unlocked_at = now();
        }

        $progress->attempts_count = ($progress->attempts_count ?? 0) + 1;

        if ($attempt->score >= ($progress->best_score ?? 0)) {
            $progress->best_wpm = $attempt->wpm;
            $progress->best_accuracy = $attempt->accuracy;
            $progress->best_score = $attempt->score;
            $progress->best_stars = $attempt->stars;
        }

        if ($attempt->completed && !$progress->is_completed) {
            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();

        if ($attempt->completed) {
            $this->unlockNextLevel($attempt);
        }

        return $progress;
    }

    private function calculateStars(bool $completed, float $wpm, float $accuracy, Level $level): int
    {
        if (!$completed) {
            return 0;
        }

        if ($wpm >= $level->target_wpm && $accuracy >= 95) {
            return 3;
        }

        if ($wpm >= ($level->target_wpm * 0.8) && $accuracy >= $level->min_accuracy) {
            return 2;
        }

        return 1;
    }

    private function unlockNextLevel(TypingAttempt $attempt): void
    {
        $currentLevel = $attempt->level;

        $nextLevel = Level::where('level_number', $currentLevel->level_number + 1)->first();

        if (!$nextLevel) {
            return;
        }

        $nextProgress = PlayerProgress::firstOrNew([
            'user_id' => $attempt->user_id,
            'level_id' => $nextLevel->id,
        ]);

        if (!$nextProgress->unlocked_at) {
            $nextProgress->unlocked_at = now();
        }

        $nextProgress->save();
    }
}
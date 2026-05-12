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
        $comparisonLength = max($typedLength, $targetLength);

        $correctChars = 0;
        $wrongChars = 0;

        for ($i = 0; $i < $comparisonLength; $i++) {
            $typedChar = $i < $typedLength ? mb_substr($typedText, $i, 1) : null;
            $targetChar = $i < $targetLength ? mb_substr($targetText, $i, 1) : null;

            if ($typedChar !== null && $typedChar === $targetChar) {
                $correctChars++;
                continue;
            }

            $wrongChars++;
        }

        $finishedAt = now();
        $startedAt = $attempt->started_at ?? now();

        $durationMs = max(1000, $finishedAt->diffInMilliseconds($startedAt));
        $durationSeconds = $durationMs / 1000;
        $durationMinutes = $durationMs / 60000;

        $accuracy = $comparisonLength > 0
            ? round(($correctChars / $comparisonLength) * 100, 2)
            : 0;

        $wpm = $durationMinutes > 0
            ? round(($correctChars / 5) / $durationMinutes, 2)
            : 0;

        $mistakes = $wrongChars;
        $exactTextMatch = $typedText === $targetText;
        $withinTimeLimit = $level->time_limit_seconds === null
            || $durationSeconds <= (float) $level->time_limit_seconds;
        $withinMistakeLimit = $level->max_mistakes === null
            || $mistakes <= (int) $level->max_mistakes;
        $meetsAccuracy = $accuracy >= (float) $level->min_accuracy;
        $meetsWpm = $wpm >= (float) $level->target_wpm;

        $passedRequirements = [
            'exact_text_match' => $exactTextMatch,
            'within_time_limit' => $withinTimeLimit,
            'within_mistake_limit' => $withinMistakeLimit,
            'meets_accuracy' => $meetsAccuracy,
            'meets_wpm' => $meetsWpm,
        ];

        $failedRules = $this->failedRules($passedRequirements);
        $completed = count($failedRules) === 0;

        $score = $this->calculateScore(
            $completed,
            $wpm,
            $accuracy,
            $mistakes,
            $durationSeconds,
            $level
        );

        $stars = $this->calculateStars($completed, $wpm, $accuracy, $mistakes, $durationSeconds, $level);

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
            'meta' => [
                'passed_requirements' => $passedRequirements,
                'failed_rules' => $failedRules,
                'target_wpm' => (float) $level->target_wpm,
                'min_accuracy' => (float) $level->min_accuracy,
                'time_limit_seconds' => $level->time_limit_seconds,
                'max_mistakes' => $level->max_mistakes,
            ],
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

    private function calculateScore(
        bool $completed,
        float $wpm,
        float $accuracy,
        int $mistakes,
        float $durationSeconds,
        Level $level
    ): int {
        if (!$completed) {
            return max(0, (int) round(($wpm * 4) + ($accuracy * 2) - ($mistakes * 5)));
        }

        $timeBonus = 0;

        if ($level->time_limit_seconds) {
            $remainingSeconds = max(0, (float) $level->time_limit_seconds - $durationSeconds);
            $timeBonus = $remainingSeconds * 2;
        }

        $targetBonus = max(0, $wpm - (float) $level->target_wpm) * 8;
        $accuracyBonus = max(0, $accuracy - (float) $level->min_accuracy) * 4;
        $mistakePenalty = $mistakes * 6;

        return max(
            0,
            (int) round(500 + ($wpm * 10) + $targetBonus + $accuracyBonus + $timeBonus - $mistakePenalty)
        );
    }

    private function calculateStars(
        bool $completed,
        float $wpm,
        float $accuracy,
        int $mistakes,
        float $durationSeconds,
        Level $level
    ): int {
        if (!$completed) {
            return 0;
        }

        $timeLimit = $level->time_limit_seconds ? (float) $level->time_limit_seconds : null;
        $fastEnough = $timeLimit === null || $durationSeconds <= ($timeLimit * 0.85);

        if (
            $wpm >= ((float) $level->target_wpm * 1.15)
            && $accuracy >= 95
            && $mistakes === 0
            && $fastEnough
        ) {
            return 3;
        }

        if ($wpm >= (float) $level->target_wpm && $accuracy >= ((float) $level->min_accuracy + 5)) {
            return 2;
        }

        return 1;
    }

    private function failedRules(array $passedRequirements): array
    {
        $labels = [
            'exact_text_match' => 'Teks belum sama persis dengan target.',
            'within_time_limit' => 'Durasi melewati batas waktu level.',
            'within_mistake_limit' => 'Jumlah kesalahan melewati batas maksimal.',
            'meets_accuracy' => 'Akurasi belum mencapai minimum level.',
            'meets_wpm' => 'WPM belum mencapai target level.',
        ];

        $failedRules = [];

        foreach ($passedRequirements as $key => $passed) {
            if (!$passed) {
                $failedRules[] = [
                    'code' => $key,
                    'message' => $labels[$key],
                ];
            }
        }

        return $failedRules;
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

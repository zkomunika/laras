<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\TypingAttempt;
use App\Services\TypingGameService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function start(Request $request)
    {
        $validated = $request->validate([
            'level_id' => ['required', 'exists:levels,id'],
        ]);

        $attempt = TypingAttempt::create([
            'user_id' => $request->user()->id,
            'level_id' => $validated['level_id'],
            'started_at' => now(),
        ]);

        return response()->json([
            'message' => 'Game started.',
            'data' => [
                'id' => $attempt->id,
                'level_id' => $attempt->level_id,
                'started_at' => $attempt->started_at,
            ],
        ]);
    }

    public function submit(Request $request, TypingGameService $typingGameService)
    {
        $validated = $request->validate([
            'attempt_id' => ['required', 'exists:typing_attempts,id'],
            'typed_text' => ['nullable', 'string'],
        ]);

        $attempt = TypingAttempt::with('level')
            ->where('user_id', $request->user()->id)
            ->findOrFail($validated['attempt_id']);
        $level = $attempt->level;

        $result = $typingGameService->calculate(
            $level,
            $attempt,
            $validated['typed_text'] ?? ''
        );

        $attempt->update($result);

        $attempt->refresh();
        $attempt->load('level');

        $typingGameService->updateProgress($attempt);

        return response()->json([
            'message' => 'Game result saved.',
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
            ],
        ]);
    }
}

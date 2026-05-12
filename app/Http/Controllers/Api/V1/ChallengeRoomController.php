<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChallengeParticipant;
use App\Models\ChallengeResult;
use App\Models\ChallengeRoom;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ChallengeRoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = ChallengeRoom::query()
            ->with(['master:id,name,email', 'level:id,level_number,title,target_text,target_wpm,min_accuracy,time_limit_seconds,max_mistakes'])
            ->withCount(['activeParticipants as participants_count'])
            ->whereIn('status', [ChallengeRoom::STATUS_WAITING, ChallengeRoom::STATUS_PLAYING])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (ChallengeRoom $room) => $this->roomSummary($room, $request->user()?->id));

        return response()->json([
            'data' => $rooms,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in([ChallengeRoom::TYPE_PUBLIC, ChallengeRoom::TYPE_PRIVATE])],
            'capacity' => ['required', 'integer', Rule::in([2, 4, 8])],
            'level_id' => ['nullable', 'exists:levels,id'],
        ]);

        $level = isset($validated['level_id'])
            ? Level::findOrFail($validated['level_id'])
            : Level::orderBy('level_number')->firstOrFail();

        $room = DB::transaction(function () use ($request, $validated, $level) {
            $room = ChallengeRoom::create([
                'master_user_id' => $request->user()->id,
                'level_id' => $level->id,
                'name' => $validated['name'],
                'type' => $validated['type'],
                'code' => $validated['type'] === ChallengeRoom::TYPE_PRIVATE ? $this->generateRoomCode() : null,
                'capacity' => $validated['capacity'],
                'status' => ChallengeRoom::STATUS_WAITING,
            ]);

            ChallengeParticipant::create([
                'challenge_room_id' => $room->id,
                'user_id' => $request->user()->id,
                'is_ready' => true,
                'status' => ChallengeParticipant::STATUS_WAITING,
            ]);

            return $room;
        });

        return response()->json([
            'message' => 'Room challenge berhasil dibuat.',
            'data' => $this->freshRoom($room, $request->user()->id),
        ], 201);
    }

    public function show(Request $request, ChallengeRoom $room)
    {
        return response()->json([
            'data' => $this->freshRoom($room, $request->user()->id),
        ]);
    }

    public function join(Request $request, ChallengeRoom $room)
    {
        if ($room->type !== ChallengeRoom::TYPE_PUBLIC) {
            throw ValidationException::withMessages([
                'room' => 'Room private membutuhkan kode room.',
            ]);
        }

        return $this->joinRoom($request, $room);
    }

    public function joinCode(Request $request, ChallengeRoom $room)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:12'],
        ]);

        if ($room->type !== ChallengeRoom::TYPE_PRIVATE) {
            throw ValidationException::withMessages([
                'room' => 'Room ini bukan private room.',
            ]);
        }

        if (strtoupper($validated['code']) !== strtoupper((string) $room->code)) {
            throw ValidationException::withMessages([
                'code' => 'Kode room tidak sesuai.',
            ]);
        }

        return $this->joinRoom($request, $room);
    }

    public function leave(Request $request, ChallengeRoom $room)
    {
        $participant = $this->participantFor($room, $request->user()->id);

        if (!$participant) {
            return response()->json([
                'message' => 'Kamu tidak sedang berada di room ini.',
                'data' => $this->freshRoom($room, $request->user()->id),
            ]);
        }

        DB::transaction(function () use ($room, $participant, $request) {
            if ((int) $room->master_user_id === (int) $request->user()->id && $room->status === ChallengeRoom::STATUS_WAITING) {
                $room->update([
                    'status' => ChallengeRoom::STATUS_CANCELLED,
                    'finished_at' => now(),
                ]);

                $room->participants()->update([
                    'status' => ChallengeParticipant::STATUS_LEFT,
                ]);

                return;
            }

            if ($room->status === ChallengeRoom::STATUS_WAITING) {
                $participant->delete();
                return;
            }

            $participant->update([
                'status' => ChallengeParticipant::STATUS_LEFT,
                'finished_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Keluar dari room berhasil.',
            'data' => $this->freshRoom($room, $request->user()->id),
        ]);
    }

    public function start(Request $request, ChallengeRoom $room)
    {
        if ((int) $room->master_user_id !== (int) $request->user()->id) {
            throw ValidationException::withMessages([
                'room' => 'Hanya room master yang bisa memulai challenge.',
            ]);
        }

        if ($room->status !== ChallengeRoom::STATUS_WAITING) {
            throw ValidationException::withMessages([
                'room' => 'Room ini sudah tidak berada pada status menunggu.',
            ]);
        }

        $participantsCount = $room->activeParticipants()->count();

        if ($participantsCount < 1) {
            throw ValidationException::withMessages([
                'room' => 'Minimal harus ada satu pemain untuk mode demo challenge.',
            ]);
        }

        DB::transaction(function () use ($room) {
            $room->update([
                'status' => ChallengeRoom::STATUS_PLAYING,
                'started_at' => now(),
            ]);

            $room->participants()->where('status', '!=', ChallengeParticipant::STATUS_LEFT)->update([
                'status' => ChallengeParticipant::STATUS_PLAYING,
                'progress_percent' => 0,
                'typed_chars' => 0,
                'mistakes' => 0,
                'wpm' => 0,
                'accuracy' => 0,
                'finished_at' => null,
            ]);
        });

        return response()->json([
            'message' => 'Challenge dimulai.',
            'data' => $this->freshRoom($room, $request->user()->id),
        ]);
    }

    public function progress(Request $request, ChallengeRoom $room)
    {
        $validated = $request->validate([
            'progress_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'typed_chars' => ['nullable', 'integer', 'min:0'],
            'mistakes' => ['nullable', 'integer', 'min:0'],
            'wpm' => ['nullable', 'numeric', 'min:0'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $participant = $this->participantFor($room, $request->user()->id);

        if (!$participant) {
            throw ValidationException::withMessages([
                'room' => 'Kamu belum bergabung ke room ini.',
            ]);
        }

        if ($room->status !== ChallengeRoom::STATUS_PLAYING) {
            throw ValidationException::withMessages([
                'room' => 'Challenge belum dimulai.',
            ]);
        }

        $participant->update([
            'progress_percent' => $validated['progress_percent'],
            'typed_chars' => $validated['typed_chars'] ?? $participant->typed_chars,
            'mistakes' => $validated['mistakes'] ?? $participant->mistakes,
            'wpm' => $validated['wpm'] ?? $participant->wpm,
            'accuracy' => $validated['accuracy'] ?? $participant->accuracy,
        ]);

        return response()->json([
            'message' => 'Progress challenge diperbarui.',
            'data' => $this->freshRoom($room, $request->user()->id),
        ]);
    }

    public function submit(Request $request, ChallengeRoom $room)
    {
        $validated = $request->validate([
            'typed_text' => ['nullable', 'string'],
        ]);

        $participant = $this->participantFor($room, $request->user()->id);

        if (!$participant) {
            throw ValidationException::withMessages([
                'room' => 'Kamu belum bergabung ke room ini.',
            ]);
        }

        if (!in_array($room->status, [ChallengeRoom::STATUS_PLAYING, ChallengeRoom::STATUS_FINISHED], true)) {
            throw ValidationException::withMessages([
                'room' => 'Challenge belum bisa dikirim.',
            ]);
        }

        $result = DB::transaction(function () use ($room, $participant, $request, $validated) {
            $calculation = $this->calculateChallengeResult($room, $validated['typed_text'] ?? '');

            $result = ChallengeResult::updateOrCreate(
                [
                    'challenge_room_id' => $room->id,
                    'user_id' => $request->user()->id,
                ],
                [
                    'level_id' => $room->level_id,
                    'typed_text' => $validated['typed_text'] ?? '',
                    'wpm' => $calculation['wpm'],
                    'accuracy' => $calculation['accuracy'],
                    'mistakes' => $calculation['mistakes'],
                    'score' => $calculation['score'],
                    'duration_ms' => $calculation['duration_ms'],
                    'completed' => $calculation['completed'],
                    'failed_rules' => $calculation['failed_rules'],
                ]
            );

            $participant->update([
                'status' => ChallengeParticipant::STATUS_FINISHED,
                'progress_percent' => 100,
                'typed_chars' => mb_strlen($validated['typed_text'] ?? ''),
                'mistakes' => $calculation['mistakes'],
                'wpm' => $calculation['wpm'],
                'accuracy' => $calculation['accuracy'],
                'finished_at' => now(),
            ]);

            $this->recalculateRanks($room);
            $this->finishRoomIfDone($room);

            return $result->fresh();
        });

        return response()->json([
            'message' => 'Hasil challenge disimpan.',
            'data' => [
                'result' => $this->formatResult($result),
                'room' => $this->freshRoom($room, $request->user()->id),
            ],
        ]);
    }

    public function results(Request $request, ChallengeRoom $room)
    {
        $room = $room->fresh(['master:id,name,email', 'level', 'participants.user:id,name,email', 'results.user:id,name,email']);

        return response()->json([
            'data' => [
                'room' => $this->roomSummary($room, $request->user()->id),
                'results' => $room->results
                    ->sortBy(fn (ChallengeResult $result) => $result->rank ?? 999)
                    ->values()
                    ->map(fn (ChallengeResult $result) => $this->formatResult($result)),
            ],
        ]);
    }

    private function joinRoom(Request $request, ChallengeRoom $room)
    {
        if ($room->status !== ChallengeRoom::STATUS_WAITING) {
            throw ValidationException::withMessages([
                'room' => 'Room sudah dimulai atau selesai.',
            ]);
        }

        $participant = $this->participantFor($room, $request->user()->id);

        if (!$participant && $room->activeParticipants()->count() >= $room->capacity) {
            throw ValidationException::withMessages([
                'room' => 'Room sudah penuh.',
            ]);
        }

        if (!$participant) {
            ChallengeParticipant::create([
                'challenge_room_id' => $room->id,
                'user_id' => $request->user()->id,
                'status' => ChallengeParticipant::STATUS_WAITING,
            ]);
        }

        return response()->json([
            'message' => 'Berhasil masuk room.',
            'data' => $this->freshRoom($room, $request->user()->id),
        ]);
    }

    private function participantFor(ChallengeRoom $room, int $userId): ?ChallengeParticipant
    {
        return ChallengeParticipant::where('challenge_room_id', $room->id)
            ->where('user_id', $userId)
            ->first();
    }

    private function freshRoom(ChallengeRoom $room, int $userId): array
    {
        $room = $room->fresh([
            'master:id,name,email',
            'level:id,chapter_id,level_number,title,story_text,target_text,target_wpm,min_accuracy,time_limit_seconds,max_mistakes,is_boss_level',
            'level.chapter:id,number,title',
            'participants.user:id,name,email',
            'results.user:id,name,email',
        ]);

        return [
            ...$this->roomSummary($room, $userId),
            'level' => $this->formatLevel($room->level),
            'participants' => $room->participants
                ->sortBy('id')
                ->values()
                ->map(fn (ChallengeParticipant $participant) => $this->formatParticipant($participant, $room))
                ->values(),
            'results' => $room->results
                ->sortBy(fn (ChallengeResult $result) => $result->rank ?? 999)
                ->values()
                ->map(fn (ChallengeResult $result) => $this->formatResult($result))
                ->values(),
            'current_participant' => ($currentParticipant = $room->participants->firstWhere('user_id', $userId))
                ? $this->formatParticipant($currentParticipant, $room)
                : null,
        ];
    }

    private function roomSummary(ChallengeRoom $room, ?int $userId): array
    {
        $participantsCount = $room->participants_count ?? $room->activeParticipants()->count();
        $isJoined = $userId
            ? ChallengeParticipant::where('challenge_room_id', $room->id)->where('user_id', $userId)->exists()
            : false;

        return [
            'id' => $room->id,
            'name' => $room->name,
            'type' => $room->type,
            'is_private' => $room->type === ChallengeRoom::TYPE_PRIVATE,
            'code' => $isJoined ? $room->code : null,
            'capacity' => $room->capacity,
            'status' => $room->status,
            'started_at' => optional($room->started_at)->toISOString(),
            'finished_at' => optional($room->finished_at)->toISOString(),
            'participants_count' => (int) $participantsCount,
            'is_full' => (int) $participantsCount >= (int) $room->capacity,
            'master' => $room->master ? [
                'id' => $room->master->id,
                'name' => $room->master->name,
                'email' => $room->master->email,
            ] : null,
            'level_summary' => $room->level ? [
                'id' => $room->level->id,
                'level_number' => $room->level->level_number,
                'title' => $room->level->title,
                'target_wpm' => $room->level->target_wpm,
                'min_accuracy' => $room->level->min_accuracy,
            ] : null,
            'is_master' => $userId ? (int) $room->master_user_id === (int) $userId : false,
            'is_joined' => $isJoined,
            'can_start' => $userId ? (int) $room->master_user_id === (int) $userId && $room->status === ChallengeRoom::STATUS_WAITING : false,
        ];
    }

    private function formatParticipant(ChallengeParticipant $participant, ChallengeRoom $room): array
    {
        return [
            'id' => $participant->id,
            'user_id' => $participant->user_id,
            'name' => $participant->user?->name ?? 'Pemain',
            'email' => $participant->user?->email,
            'is_master' => (int) $room->master_user_id === (int) $participant->user_id,
            'is_ready' => $participant->is_ready,
            'progress_percent' => round((float) $participant->progress_percent, 2),
            'typed_chars' => $participant->typed_chars,
            'mistakes' => $participant->mistakes,
            'wpm' => round((float) $participant->wpm, 2),
            'accuracy' => round((float) $participant->accuracy, 2),
            'status' => $participant->status,
            'finished_at' => optional($participant->finished_at)->toISOString(),
        ];
    }

    private function formatResult(ChallengeResult $result): array
    {
        return [
            'id' => $result->id,
            'user_id' => $result->user_id,
            'name' => $result->user?->name ?? 'Pemain',
            'email' => $result->user?->email,
            'rank' => $result->rank,
            'wpm' => round((float) $result->wpm, 2),
            'accuracy' => round((float) $result->accuracy, 2),
            'mistakes' => $result->mistakes,
            'score' => $result->score,
            'duration_ms' => $result->duration_ms,
            'duration_seconds' => round($result->duration_ms / 1000, 2),
            'completed' => $result->completed,
            'failed_rules' => $result->failed_rules ?? [],
        ];
    }

    private function formatLevel(?Level $level): ?array
    {
        if (!$level) {
            return null;
        }

        return [
            'id' => $level->id,
            'chapter' => $level->chapter ? [
                'id' => $level->chapter->id,
                'number' => $level->chapter->number,
                'title' => $level->chapter->title,
            ] : null,
            'level_number' => $level->level_number,
            'title' => $level->title,
            'story_text' => $level->story_text,
            'target_text' => $level->target_text,
            'target_wpm' => $level->target_wpm,
            'min_accuracy' => $level->min_accuracy,
            'time_limit_seconds' => $level->time_limit_seconds,
            'max_mistakes' => $level->max_mistakes,
            'is_boss_level' => $level->is_boss_level,
        ];
    }

    private function calculateChallengeResult(ChallengeRoom $room, string $typedText): array
    {
        $level = $room->level ?: Level::orderBy('level_number')->firstOrFail();
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

        $startedAt = $room->started_at ?? now();
        $durationMs = max(1000, now()->diffInMilliseconds($startedAt));
        $durationSeconds = $durationMs / 1000;
        $durationMinutes = $durationMs / 60000;

        $accuracy = $comparisonLength > 0 ? round(($correctChars / $comparisonLength) * 100, 2) : 0;
        $wpm = $durationMinutes > 0 ? round(($correctChars / 5) / $durationMinutes, 2) : 0;
        $mistakes = $wrongChars;

        $failedRules = [];

        if ($typedText !== $targetText) {
            $failedRules[] = ['code' => 'exact_text_match', 'message' => 'Teks belum sama persis dengan target.'];
        }

        if ($level->time_limit_seconds !== null && $durationSeconds > (float) $level->time_limit_seconds) {
            $failedRules[] = ['code' => 'within_time_limit', 'message' => 'Durasi melewati batas waktu level.'];
        }

        if ($level->max_mistakes !== null && $mistakes > (int) $level->max_mistakes) {
            $failedRules[] = ['code' => 'within_mistake_limit', 'message' => 'Jumlah kesalahan melewati batas maksimal.'];
        }

        if ($accuracy < (float) $level->min_accuracy) {
            $failedRules[] = ['code' => 'meets_accuracy', 'message' => 'Akurasi belum mencapai minimum level.'];
        }

        if ($wpm < (float) $level->target_wpm) {
            $failedRules[] = ['code' => 'meets_wpm', 'message' => 'WPM belum mencapai target level.'];
        }

        $completed = count($failedRules) === 0;
        $score = $completed
            ? max(0, (int) round(700 + ($wpm * 10) + ($accuracy * 4) - ($mistakes * 8)))
            : max(0, (int) round(($wpm * 4) + ($accuracy * 2) - ($mistakes * 5)));

        return [
            'wpm' => $wpm,
            'accuracy' => $accuracy,
            'mistakes' => $mistakes,
            'score' => $score,
            'duration_ms' => $durationMs,
            'completed' => $completed,
            'failed_rules' => $failedRules,
        ];
    }

    private function recalculateRanks(ChallengeRoom $room): void
    {
        $results = ChallengeResult::where('challenge_room_id', $room->id)
            ->orderByDesc('completed')
            ->orderBy('duration_ms')
            ->orderByDesc('accuracy')
            ->orderByDesc('wpm')
            ->orderBy('mistakes')
            ->orderByDesc('score')
            ->get();

        $rank = 1;

        foreach ($results as $result) {
            $result->update(['rank' => $rank]);
            $rank++;
        }
    }

    private function finishRoomIfDone(ChallengeRoom $room): void
    {
        $activeCount = $room->participants()
            ->where('status', '!=', ChallengeParticipant::STATUS_LEFT)
            ->count();

        $finishedCount = $room->participants()
            ->where('status', ChallengeParticipant::STATUS_FINISHED)
            ->count();

        if ($activeCount > 0 && $finishedCount >= $activeCount) {
            $room->update([
                'status' => ChallengeRoom::STATUS_FINISHED,
                'finished_at' => now(),
            ]);
        }
    }

    private function generateRoomCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (ChallengeRoom::where('code', $code)->exists());

        return $code;
    }
}

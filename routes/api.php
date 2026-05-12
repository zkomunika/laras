<?php

use App\Http\Controllers\Api\V1\ChapterController;
use App\Http\Controllers\Api\V1\LevelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\GameController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\LeaderboardController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RealtimeController;
use App\Http\Controllers\Api\V1\AchievementController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ChallengeRoomController;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'app' => 'LARAS API',
        ]);
    });

    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::get('/chapters', [ChapterController::class, 'index']);
    Route::get('/chapters/{chapter}', [ChapterController::class, 'show']);
    Route::get('/chapters/{chapter}/levels', [ChapterController::class, 'levels']);

    Route::get('/levels', [LevelController::class, 'index']);
    Route::get('/levels/{level}', [LevelController::class, 'show']);

    Route::get('/leaderboard', [LeaderboardController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::post('/game/start', [GameController::class, 'start']);
        Route::post('/game/submit', [GameController::class, 'submit']);

        Route::get('/progress', [ProgressController::class, 'index']);
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::get('/achievements', [AchievementController::class, 'index']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

        Route::post('/realtime/heartbeat', [RealtimeController::class, 'heartbeat']);

        Route::get('/realtime/online-users', [RealtimeController::class, 'onlineUsers']);
        Route::get('/realtime/messages', [RealtimeController::class, 'messages']);
        Route::post('/realtime/messages', [RealtimeController::class, 'sendMessage']);

        Route::get('/challenge/rooms', [ChallengeRoomController::class, 'index']);
        Route::post('/challenge/rooms', [ChallengeRoomController::class, 'store']);
        Route::get('/challenge/rooms/{room}', [ChallengeRoomController::class, 'show']);
        Route::post('/challenge/rooms/{room}/join', [ChallengeRoomController::class, 'join']);
        Route::post('/challenge/rooms/{room}/join-code', [ChallengeRoomController::class, 'joinCode']);
        Route::post('/challenge/rooms/{room}/leave', [ChallengeRoomController::class, 'leave']);
        Route::post('/challenge/rooms/{room}/start', [ChallengeRoomController::class, 'start']);
        Route::post('/challenge/rooms/{room}/progress', [ChallengeRoomController::class, 'progress']);
        Route::post('/challenge/rooms/{room}/submit', [ChallengeRoomController::class, 'submit']);
        Route::get('/challenge/rooms/{room}/results', [ChallengeRoomController::class, 'results']);
    });
});

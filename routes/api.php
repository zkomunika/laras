<?php

use App\Http\Controllers\Api\V1\ChapterController;
use App\Http\Controllers\Api\V1\LevelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\GameController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\LeaderboardController;
use App\Http\Controllers\Api\V1\ProfileController;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'app' => 'LARAS API',
        ]);
    });

    Route::get('/chapters', [ChapterController::class, 'index']);
    Route::get('/chapters/{chapter}', [ChapterController::class, 'show']);
    Route::get('/chapters/{chapter}/levels', [ChapterController::class, 'levels']);

    Route::get('/levels', [LevelController::class, 'index']);
    Route::get('/levels/{level}', [LevelController::class, 'show']);

    Route::post('/game/start', [GameController::class, 'start']);
    Route::post('/game/submit', [GameController::class, 'submit']);
    Route::get('/progress', [ProgressController::class, 'index']);
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
});

<?php

use App\Http\Controllers\Api\V1\ChapterController;
use App\Http\Controllers\Api\V1\LevelController;
use Illuminate\Support\Facades\Route;

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
});
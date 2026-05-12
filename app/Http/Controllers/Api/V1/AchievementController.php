<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request, AchievementService $achievementService)
    {
        return response()->json([
            'data' => $achievementService->achievementListForUser($request->user()),
        ]);
    }
}

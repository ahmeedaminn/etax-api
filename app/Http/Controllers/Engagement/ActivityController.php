<?php

namespace App\Http\Controllers\Engagement;

use App\Http\Controllers\Controller;
use App\Services\Engagement\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct(
        protected ActivityService $activityService
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $activity = $this->activityService->getUserActivity(
            $request->user()
        );

        return response()->json([
            'status' => 'success',
            'data' => $activity,
        ]);
    }
}
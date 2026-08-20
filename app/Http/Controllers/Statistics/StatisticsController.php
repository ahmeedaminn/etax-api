<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Services\Statistics\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __construct(
        protected StatisticsService $statisticsService,
    ) {}

    public function institution(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->statisticsService->getInstitutionStatistics(
                $request->user(),
            ),
        ]);
    }

    public function platform(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->statisticsService->getPlatformStatistics(),
        ]);
    }
}

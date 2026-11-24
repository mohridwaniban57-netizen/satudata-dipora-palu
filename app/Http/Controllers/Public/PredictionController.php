<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PredictionResult;
use App\Models\PredictionRun;
use App\Services\Analytics\LevyInsightService;
use App\Services\Analytics\MaintenanceScoreService;
use App\Services\Analytics\MedalProjectionService;
use Illuminate\Http\JsonResponse;

class PredictionController extends Controller
{
    public function __construct(
        protected MedalProjectionService $medalService,
        protected MaintenanceScoreService $maintenanceService,
        protected LevyInsightService $levyService
    ) {}

    /**
     * Get medal projections for next event.
     */
    public function medals(): JsonResponse
    {
        $latestRun = PredictionRun::latest('run_at')->first();

        if ($latestRun) {
            $result = PredictionResult::where('run_id', $latestRun->id)
                ->where('kind', 'medal')
                ->first();

            if ($result && !empty($result->payload)) {
                return response()->json([
                    'data' => $result->payload,
                    'meta' => [
                        'snapshot_id' => $latestRun->id,
                        'run_at' => $latestRun->run_at,
                        'source' => 'snapshot',
                    ],
                ]);
            }
        }

        // Fallback: compute on the fly
        $data = $this->medalService->run();

        return response()->json([
            'data' => $data,
            'meta' => [
                'source' => 'on_the_fly',
                'run_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get maintenance condition scores & priority ranking.
     */
    public function maintenance(): JsonResponse
    {
        $latestRun = PredictionRun::latest('run_at')->first();

        if ($latestRun) {
            $result = PredictionResult::where('run_id', $latestRun->id)
                ->where('kind', 'maintenance')
                ->first();

            if ($result && !empty($result->payload)) {
                return response()->json([
                    'data' => $result->payload,
                    'meta' => [
                        'snapshot_id' => $latestRun->id,
                        'run_at' => $latestRun->run_at,
                        'source' => 'snapshot',
                    ],
                ]);
            }
        }

        // Fallback: compute on the fly
        $data = $this->maintenanceService->run();

        return response()->json([
            'data' => $data,
            'meta' => [
                'source' => 'on_the_fly',
                'run_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get venue levy quadrant insights & recommendations.
     */
    public function levy(): JsonResponse
    {
        $latestRun = PredictionRun::latest('run_at')->first();

        if ($latestRun) {
            $result = PredictionResult::where('run_id', $latestRun->id)
                ->where('kind', 'levy')
                ->first();

            if ($result && !empty($result->payload)) {
                return response()->json([
                    'data' => $result->payload,
                    'meta' => [
                        'snapshot_id' => $latestRun->id,
                        'run_at' => $latestRun->run_at,
                        'source' => 'snapshot',
                    ],
                ]);
            }
        }

        // Fallback: compute on the fly
        $data = $this->levyService->run();

        return response()->json([
            'data' => $data,
            'meta' => [
                'source' => 'on_the_fly',
                'run_at' => now()->toIso8601String(),
            ],
        ]);
    }
}

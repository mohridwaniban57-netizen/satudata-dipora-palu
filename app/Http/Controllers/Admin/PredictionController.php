<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PredictionResult;
use App\Models\PredictionRun;
use App\Services\Analytics\LevyInsightService;
use App\Services\Analytics\MaintenanceScoreService;
use App\Services\Analytics\MedalProjectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PredictionController extends Controller
{
    public function __construct(
        protected MedalProjectionService $medalService,
        protected MaintenanceScoreService $maintenanceService,
        protected LevyInsightService $levyService
    ) {}

    /**
     * List previous prediction snapshot runs.
     */
    public function index(): JsonResponse
    {
        $runs = PredictionRun::with('user')
            ->withCount('results')
            ->orderBy('run_at', 'desc')
            ->paginate(15);

        return response()->json($runs);
    }

    /**
     * Trigger fresh calculation and persist snapshots into prediction_runs and prediction_results.
     * POST /admin/predictions/run
     */
    public function run(): JsonResponse
    {
        $medalResults = $this->medalService->run();
        $maintenanceResults = $this->maintenanceService->run();
        $levyResults = $this->levyService->run();

        DB::beginTransaction();
        try {
            $run = PredictionRun::create([
                'run_at' => now(),
                'user_id' => Auth::id(),
                'config_snapshot' => config('analytics'),
            ]);

            // Save Medal Results
            PredictionResult::create([
                'run_id' => $run->id,
                'kind' => 'medal',
                'subject_ref' => 'all_sports',
                'payload' => $medalResults,
            ]);

            // Save Maintenance Results
            PredictionResult::create([
                'run_id' => $run->id,
                'kind' => 'maintenance',
                'subject_ref' => 'all_venues',
                'payload' => $maintenanceResults,
            ]);

            // Save Levy Results
            PredictionResult::create([
                'run_id' => $run->id,
                'kind' => 'levy',
                'subject_ref' => 'all_venues',
                'payload' => $levyResults,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menyimpan snapshot hasil prediksi: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Kalkulasi prediksi prediktif berhasil dijalankan dan disimpan sebagai snapshot baru.',
            'data' => [
                'run_id' => $run->id,
                'run_at' => $run->run_at,
                'summary' => [
                    'projected_medals' => $medalResults['total_projected_gold'],
                    'venues_scored' => count($maintenanceResults['venues']),
                    'levy_quadrants' => $levyResults['quadrant_summary'],
                ],
            ],
        ]);
    }
}

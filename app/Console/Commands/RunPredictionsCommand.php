<?php

namespace App\Console\Commands;

use App\Models\PredictionResult;
use App\Models\PredictionRun;
use App\Models\User;
use App\Services\Analytics\LevyInsightService;
use App\Services\Analytics\MaintenanceScoreService;
use App\Services\Analytics\MedalProjectionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunPredictionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'predictions:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run predictive analytics engine and save snapshot to prediction_runs & prediction_results';

    /**
     * Execute the console command.
     */
    public function handle(
        MedalProjectionService $medalService,
        MaintenanceScoreService $maintenanceService,
        LevyInsightService $levyService
    ): int {
        $this->info('Running predictive analytics engine for Satu Data Dispora Palu...');

        $medalResults = $medalService->run();
        $this->info("✓ Medal projections calculated (projected gold: {$medalResults['total_projected_gold']})");

        $maintenanceResults = $maintenanceService->run();
        $this->info("✓ Maintenance scoring completed (venues scored: " . count($maintenanceResults['venues']) . ")");

        $levyResults = $levyService->run();
        $this->info("✓ Levy optimization matrix classified (venues: " . count($levyResults['venues']) . ")");

        DB::beginTransaction();
        try {
            $user = User::first();

            $run = PredictionRun::create([
                'run_at' => now(),
                'user_id' => $user?->id ?? 1,
                'config_snapshot' => config('analytics'),
            ]);

            PredictionResult::create([
                'run_id' => $run->id,
                'kind' => 'medal',
                'subject_ref' => 'all_sports',
                'payload' => $medalResults,
            ]);

            PredictionResult::create([
                'run_id' => $run->id,
                'kind' => 'maintenance',
                'subject_ref' => 'all_venues',
                'payload' => $maintenanceResults,
            ]);

            PredictionResult::create([
                'run_id' => $run->id,
                'kind' => 'levy',
                'subject_ref' => 'all_venues',
                'payload' => $levyResults,
            ]);

            DB::commit();
            $this->info("✓ Prediction snapshot #{$run->id} persisted successfully!");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Failed to persist prediction snapshot: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

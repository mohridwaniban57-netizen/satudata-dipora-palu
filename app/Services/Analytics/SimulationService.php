<?php

namespace App\Services\Analytics;

use App\Models\Budget;
use App\Models\MedalHistory;
use App\Models\Sport;
use App\Models\Venue;
use Carbon\Carbon;

class SimulationService
{
    public function __construct(
        protected MedalProjectionService $medalProjection,
        protected MaintenanceScoreService $maintenanceScore,
        protected LinearRegression $regression
    ) {}

    /**
     * Simulate coaching budget adjustment for a sport.
     *
     * @param int $sportId
     * @param float $changePct e.g. 15.0 for +15%, -10.0 for -10%
     */
    public function simulateBudget(int $sportId, float $changePct): array
    {
        $sport = Sport::findOrFail($sportId);

        // 1. Get baseline medal projection
        $baseline = $this->medalProjection->projectForSport($sportId);
        $baselinePoints = (float) $baseline['projected_points'];
        $baselineGold = (int) $baseline['projected_gold'];

        // 2. Get baseline budget
        $latestBudgetRecord = Budget::where('sport_id', $sportId)
            ->where('state', 'published')
            ->orderBy('year', 'desc')
            ->first();

        $baselineBudget = $latestBudgetRecord ? (float) $latestBudgetRecord->amount : 50000000.0;
        $deltaBudget = $baselineBudget * ($changePct / 100.0);
        $simulatedBudget = max(0.0, $baselineBudget + $deltaBudget);

        // 3. Determine beta_budget
        $pairedData = $this->getPairedBudgetMedalData($sportId);
        $defaultBeta = (float) config('analytics.simulation.default_beta_budget', 0.0000005);
        $isAssumed = true;
        $beta = $defaultBeta;

        if (count($pairedData) >= 4) {
            $xs = array_column($pairedData, 'budget');
            $ys = array_column($pairedData, 'points');
            $this->regression->fit($xs, $ys);
            $slope = $this->regression->slope();

            if ($slope > 0) {
                $beta = $slope;
                $isAssumed = false;
            }
        }

        // 4. Calculate simulated points
        $deltaPoints = $beta * $deltaBudget;
        $simulatedPoints = max(0.0, round($baselinePoints + $deltaPoints, 1));

        // Estimate gold change
        $histories = $baseline['history'];
        $totalHistoricalPoints = array_sum(array_column($histories, 'points'));
        $totalHistoricalGold = array_sum(array_column($histories, 'gold'));
        $goldRatio = $totalHistoricalPoints > 0 ? ($totalHistoricalGold / $totalHistoricalPoints) : 0.0;

        $simulatedGold = max(0, (int) round($simulatedPoints * $goldRatio));

        return [
            'sport_id' => $sport->id,
            'sport_name' => $sport->name,
            'change_pct' => $changePct,
            'beta_coefficient' => $beta,
            'is_assumption_based' => $isAssumed,
            'budget' => [
                'baseline' => $baselineBudget,
                'delta' => $deltaBudget,
                'simulated' => $simulatedBudget,
            ],
            'points' => [
                'baseline' => $baselinePoints,
                'delta' => round($simulatedPoints - $baselinePoints, 1),
                'simulated' => $simulatedPoints,
            ],
            'gold' => [
                'baseline' => $baselineGold,
                'delta' => $simulatedGold - $baselineGold,
                'simulated' => $simulatedGold,
            ],
            'confidence_label' => $isAssumed ? 'Assumption-Based' : $baseline['confidence_label'],
            'explanation' => $isAssumed
                ? "Simulasi menggunakan koefisien asumsi standar (β = " . number_format($beta, 8) . ") karena data historis anggaran dan medali kurang dari 4 periode."
                : "Simulasi menggunakan estimasi regresi OLS antara anggaran dan perolehan poin masa lalu (β = " . number_format($beta, 8) . ").",
        ];
    }

    /**
     * Simulate renovation of a venue with target inspection score.
     *
     * @param int $venueId
     * @param float $targetScore 0 - 100
     */
    public function simulateRenovation(int $venueId, float $targetScore): array
    {
        $venue = Venue::with(['venueType', 'district', 'inspections', 'maintenanceRecords', 'venueUsages'])
            ->findOrFail($venueId);

        $baseline = $this->maintenanceScore->scoreFor($venue);

        $wAge = config('analytics.maintenance.weights.age_factor', 0.30);
        $wInsp = config('analytics.maintenance.weights.inspection_deficit', 0.50);
        $wDam = config('analytics.maintenance.weights.damage_frequency', 0.20);

        $goodThresh = config('analytics.maintenance.thresholds.good', 75.0);
        $watchThresh = config('analytics.maintenance.thresholds.watch', 50.0);

        // Post-renovation assumptions:
        // Age factor: preserved or reduced slightly
        $ageFactor = $baseline['age_factor'];
        $simulatedInspectionScore = max(0.0, min(100.0, $targetScore));
        $simulatedInspectionDeficit = 100.0 - $simulatedInspectionScore;
        // Post renovation incident damage is reset to 0
        $simulatedDamageFrequency = 0.0;

        $simulatedRisk = ($wAge * $ageFactor) + ($wInsp * $simulatedInspectionDeficit) + ($wDam * $simulatedDamageFrequency);
        $simulatedRisk = max(0.0, min(100.0, round($simulatedRisk, 1)));
        $simulatedConditionScore = max(0.0, min(100.0, round(100.0 - $simulatedRisk, 1)));

        if ($simulatedConditionScore >= $goodThresh) {
            $simulatedStatus = 'Good';
        } elseif ($simulatedConditionScore >= $watchThresh) {
            $simulatedStatus = 'Watch';
        } else {
            $simulatedStatus = 'Critical';
        }

        // Maintenance extension
        $baseIntervalDays = $baseline['base_interval_days'];
        $simulatedIntervalDays = (int) round($baseIntervalDays * ($simulatedConditionScore / 100.0));
        $simulatedNextMaintenance = Carbon::now()->addDays($simulatedIntervalDays)->toDateString();

        // Usage uplift calculation
        $statusTierMap = ['Critical' => 1, 'Watch' => 2, 'Good' => 3];
        $baselineTier = $statusTierMap[$baseline['status']] ?? 1;
        $simulatedTier = $statusTierMap[$simulatedStatus] ?? 1;
        $tierImprovement = max(0, $simulatedTier - $baselineTier);

        $upliftPerTier = (float) config('analytics.simulation.renovation_status_uplift', 0.10);
        $usageUpliftPct = $tierImprovement * $upliftPerTier * 100.0;

        return [
            'venue_id' => $venue->id,
            'venue_name' => $venue->name,
            'target_inspection_score' => $simulatedInspectionScore,
            'condition_score' => [
                'baseline' => $baseline['condition_score'],
                'delta' => round($simulatedConditionScore - $baseline['condition_score'], 1),
                'simulated' => $simulatedConditionScore,
            ],
            'status' => [
                'baseline' => $baseline['status'],
                'simulated' => $simulatedStatus,
            ],
            'maintenance_date' => [
                'baseline' => $baseline['next_maintenance_date'],
                'simulated' => $simulatedNextMaintenance,
                'interval_days_extended' => $simulatedIntervalDays - $baseline['interval_days'],
            ],
            'projected_usage_uplift_pct' => $usageUpliftPct,
            'explanation' => "Renovasi dengan target inspeksi {$simulatedInspectionScore} diproyeksikan menaikkan skor kondisi dari {$baseline['condition_score']} ({$baseline['status']}) menjadi {$simulatedConditionScore} ({$simulatedStatus}), memperpanjang interval pemeliharaan, serta meningkatkan estimasi utilisasi sebesar +{$usageUpliftPct}%.",
        ];
    }

    private function getPairedBudgetMedalData(int $sportId): array
    {
        $budgets = Budget::where('sport_id', $sportId)->where('state', 'published')->get()->keyBy('year');
        $medals = MedalHistory::where('sport_id', $sportId)->where('state', 'published')->get();

        $pairs = [];
        foreach ($medals as $m) {
            $year = (int) $m->year;
            if (isset($budgets[$year])) {
                $points = (3 * $m->gold) + (2 * $m->silver) + (1 * $m->bronze);
                $pairs[] = [
                    'year' => $year,
                    'budget' => (float) $budgets[$year]->amount,
                    'points' => (float) $points,
                ];
            }
        }

        return $pairs;
    }
}

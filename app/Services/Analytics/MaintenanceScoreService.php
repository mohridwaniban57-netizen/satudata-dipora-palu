<?php

namespace App\Services\Analytics;

use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MaintenanceScoreService
{
    /**
     * Calculate score and metrics for a single venue.
     */
    public function scoreFor(Venue $venue): array
    {
        $venue->loadMissing(['venueType', 'district', 'inspections', 'maintenanceRecords', 'venueUsages']);

        $wAge = config('analytics.maintenance.weights.age_factor', 0.30);
        $wInsp = config('analytics.maintenance.weights.inspection_deficit', 0.50);
        $wDam = config('analytics.maintenance.weights.damage_frequency', 0.20);

        $goodThresh = config('analytics.maintenance.thresholds.good', 75.0);
        $watchThresh = config('analytics.maintenance.thresholds.watch', 50.0);

        $damageWindowMonths = config('analytics.maintenance.damage_window_months', 24);
        $incidentCap = config('analytics.maintenance.damage_incident_cap', 5);

        $currentYear = (int) date('Y');
        $ageYears = max(0, $currentYear - ($venue->year_built ?: $currentYear));
        $designLifeYears = $venue->venueType?->design_life_years ?: config('analytics.maintenance.default_design_life_years', 30);
        $ageFactor = min(1.0, $ageYears / max(1, $designLifeYears)) * 100.0;

        // Latest inspection
        $latestInspection = $venue->inspections->sortByDesc('inspected_on')->first();
        $latestInspectionScore = $latestInspection ? (float) $latestInspection->score : 80.0;
        $inspectionDeficit = max(0.0, 100.0 - $latestInspectionScore);

        // Incidents last 24 months
        $cutoffDate = Carbon::now()->subMonths($damageWindowMonths);
        $incidentsCount = $venue->maintenanceRecords
            ->filter(fn ($m) => $m->is_incident && Carbon::parse($m->done_on)->gte($cutoffDate))
            ->count();
        $damageFrequency = min(1.0, $incidentsCount / max(1, $incidentCap)) * 100.0;

        // Risk & Condition Score
        $riskScore = ($wAge * $ageFactor) + ($wInsp * $inspectionDeficit) + ($wDam * $damageFrequency);
        $riskScore = max(0.0, min(100.0, round($riskScore, 1)));
        $conditionScore = max(0.0, min(100.0, round(100.0 - $riskScore, 1)));

        // Status classification
        if ($conditionScore >= $goodThresh) {
            $status = 'Good';
        } elseif ($conditionScore >= $watchThresh) {
            $status = 'Watch';
        } else {
            $status = 'Critical';
        }

        // Maintenance schedule
        $baseIntervalDays = $venue->venueType?->base_interval_days ?: config('analytics.maintenance.default_base_interval_days', 180);
        $intervalFactor = max(0.1, $conditionScore / 100.0);
        $intervalDays = (int) round($baseIntervalDays * $intervalFactor);

        $latestMaintenance = $venue->maintenanceRecords->sortByDesc('done_on')->first();
        $lastMaintenanceDate = $latestMaintenance?->done_on
            ?: ($latestInspection?->inspected_on ?: Carbon::now()->toDateString());

        $nextMaintenanceDate = Carbon::parse($lastMaintenanceDate)->addDays($intervalDays)->toDateString();

        // Total visits in last 12 months for tie-break
        $cutoff12m = Carbon::now()->subMonths(12);
        $totalVisits12m = $venue->venueUsages
            ->filter(fn ($u) => Carbon::parse($u->month)->gte($cutoff12m))
            ->sum('visits');

        return [
            'venue_id' => $venue->id,
            'venue_name' => $venue->name,
            'venue_type' => $venue->venueType?->name,
            'district_name' => $venue->district?->name,
            'age_years' => $ageYears,
            'design_life_years' => $designLifeYears,
            'age_factor' => round($ageFactor, 1),
            'latest_inspection_score' => round($latestInspectionScore, 1),
            'latest_inspection_date' => $latestInspection?->inspected_on,
            'inspection_deficit' => round($inspectionDeficit, 1),
            'incidents_last_24_months' => $incidentsCount,
            'damage_frequency' => round($damageFrequency, 1),
            'risk_score' => $riskScore,
            'condition_score' => $conditionScore,
            'status' => $status,
            'base_interval_days' => $baseIntervalDays,
            'interval_days' => $intervalDays,
            'last_maintenance_date' => $lastMaintenanceDate,
            'next_maintenance_date' => $nextMaintenanceDate,
            'total_visits_12m' => (int) $totalVisits12m,
            'capacity' => (int) $venue->capacity,
        ];
    }

    /**
     * Run scoring and priority ranking for all published venues.
     */
    public function run(): array
    {
        $venues = Venue::where('state', 'published')
            ->with(['venueType', 'district', 'inspections', 'maintenanceRecords', 'venueUsages'])
            ->get();

        $scores = $venues->map(fn ($venue) => $this->scoreFor($venue))->all();

        // Sort by risk_score DESC, tie-break by total_visits_12m DESC
        usort($scores, function ($a, $b) {
            if ($b['risk_score'] == $a['risk_score']) {
                return $b['total_visits_12m'] <=> $a['total_visits_12m'];
            }
            return $b['risk_score'] <=> $a['risk_score'];
        });

        // Assign priority ranks
        $ranked = [];
        $rank = 1;
        $statusCounts = ['Good' => 0, 'Watch' => 0, 'Critical' => 0];

        foreach ($scores as $item) {
            $item['priority_rank'] = $rank++;
            $statusCounts[$item['status']] = ($statusCounts[$item['status']] ?? 0) + 1;
            $ranked[] = $item;
        }

        $total = count($ranked);
        $distribution = [
            'good_count' => $statusCounts['Good'] ?? 0,
            'good_pct' => $total > 0 ? round((($statusCounts['Good'] ?? 0) / $total) * 100, 1) : 0.0,
            'watch_count' => $statusCounts['Watch'] ?? 0,
            'watch_pct' => $total > 0 ? round((($statusCounts['Watch'] ?? 0) / $total) * 100, 1) : 0.0,
            'critical_count' => $statusCounts['Critical'] ?? 0,
            'critical_pct' => $total > 0 ? round((($statusCounts['Critical'] ?? 0) / $total) * 100, 1) : 0.0,
            'total' => $total,
        ];

        return [
            'venues' => $ranked,
            'distribution' => $distribution,
            'generated_at' => now()->toIso8601String(),
        ];
    }
}

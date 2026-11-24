<?php

namespace App\Services\Analytics;

use App\Models\MedalHistory;
use App\Models\Sport;
use Illuminate\Support\Collection;

class MedalProjectionService
{
    public function __construct(
        protected LinearRegression $regression
    ) {}

    /**
     * Run projections for all active sports.
     */
    public function run(?int $targetYear = null): array
    {
        $sports = Sport::with(['medalHistories' => function ($q) {
            $q->where('state', 'published')->orderBy('year', 'asc');
        }])->get();

        $results = [];
        $totalProjectedPoints = 0.0;
        $totalProjectedGold = 0;

        foreach ($sports as $sport) {
            $projection = $this->projectForSport($sport->id, $targetYear);
            $results[] = $projection;
            $totalProjectedPoints += $projection['projected_points'];
            $totalProjectedGold += $projection['projected_gold'];
        }

        return [
            'target_year' => $targetYear ?? (date('Y') + 1),
            'total_projected_points' => round($totalProjectedPoints, 1),
            'total_projected_gold' => (int) round($totalProjectedGold),
            'projections' => $results,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Project medals for a specific sport.
     */
    public function projectForSport(int $sportId, ?int $targetYear = null): array
    {
        $sport = Sport::findOrFail($sportId);

        $histories = MedalHistory::where('sport_id', $sportId)
            ->where('state', 'published')
            ->orderBy('year', 'asc')
            ->get();

        $n = $histories->count();
        $targetYear = $targetYear ?? ($histories->max('year') ? $histories->max('year') + 2 : (int) date('Y') + 1);

        $historyPoints = $histories->map(function ($item) {
            $points = (3 * $item->gold) + (2 * $item->silver) + (1 * $item->bronze);
            return [
                'year' => (int) $item->year,
                'event' => $item->event,
                'gold' => (int) $item->gold,
                'silver' => (int) $item->silver,
                'bronze' => (int) $item->bronze,
                'points' => $points,
            ];
        })->values();

        if ($n === 0) {
            return [
                'sport_id' => $sport->id,
                'sport_name' => $sport->name,
                'target_year' => $targetYear,
                'projected_points' => 0.0,
                'points_range_min' => 0.0,
                'points_range_max' => 0.0,
                'projected_gold' => 0,
                'confidence_label' => 'Low',
                'standard_error' => 0.0,
                'r_squared' => 0.0,
                'method' => 'No Historical Data',
                'sample_size' => 0,
                'history' => [],
            ];
        }

        $totalHistoricalPoints = $historyPoints->sum('points');
        $totalHistoricalGold = $historyPoints->sum('gold');
        $goldRatio = $totalHistoricalPoints > 0 ? ($totalHistoricalGold / $totalHistoricalPoints) : 0.0;

        if ($n < 3) {
            // Fallback to average of available events
            $avgPoints = $totalHistoricalPoints / $n;
            $projectedPoints = max(0.0, round($avgPoints, 1));
            $confidence = 'Low';
            $method = 'Historical Average Fallback';
            $standardError = 0.0;
            $rSquared = 0.0;
            $rangeMin = $projectedPoints;
            $rangeMax = $projectedPoints;
        } else {
            // OLS Linear Regression
            $xs = $historyPoints->pluck('year')->all();
            $ys = $historyPoints->pluck('points')->all();

            $this->regression->fit($xs, $ys);
            $rawProjection = $this->regression->predict($targetYear);
            $projectedPoints = max(0.0, round($rawProjection, 1));
            $standardError = round($this->regression->standardError(), 2);
            $rSquared = round($this->regression->rSquared(), 3);

            $rangeMin = max(0.0, round($projectedPoints - $standardError, 1));
            $rangeMax = round($projectedPoints + $standardError, 1);

            $confidence = $n >= 5 ? 'High' : 'Medium';
            $method = 'OLS Linear Regression';
        }

        $projectedGold = max(0, (int) round($projectedPoints * $goldRatio));

        return [
            'sport_id' => $sport->id,
            'sport_name' => $sport->name,
            'target_year' => $targetYear,
            'projected_points' => $projectedPoints,
            'points_range_min' => $rangeMin,
            'points_range_max' => $rangeMax,
            'projected_gold' => $projectedGold,
            'confidence_label' => $confidence,
            'standard_error' => $standardError,
            'r_squared' => $rSquared,
            'method' => $method,
            'sample_size' => $n,
            'history' => $historyPoints->all(),
        ];
    }
}

<?php

namespace App\Services\Analytics;

use App\Models\Venue;
use Carbon\Carbon;

class LevyInsightService
{
    public function __construct(
        protected LinearRegression $regression
    ) {}

    /**
     * Run classification and insights for all published venues.
     */
    public function run(): array
    {
        $venues = Venue::where('state', 'published')
            ->with(['venueType', 'district', 'venueRevenues', 'venueUsages', 'maintenanceRecords'])
            ->get();

        $cutoff12m = Carbon::now()->subMonths(12)->startOfMonth();

        $venueMetrics = [];

        foreach ($venues as $venue) {
            // Revenue last 12 months
            $rev12m = $venue->venueRevenues
                ->filter(fn ($r) => Carbon::parse($r->month)->gte($cutoff12m))
                ->sum('amount');

            // Usage last 12 months
            $visits12m = $venue->venueUsages
                ->filter(fn ($u) => Carbon::parse($u->month)->gte($cutoff12m))
                ->sum('visits');

            // Maintenance cost last 12 months
            $maintCost12m = $venue->maintenanceRecords
                ->filter(fn ($m) => Carbon::parse($m->done_on)->gte($cutoff12m))
                ->sum('cost');

            $capacity = max(1, (int) $venue->capacity);
            $operatingDays = config('analytics.levy.operating_days_per_year', 365);
            $areaM2 = max(1.0, (float) $venue->area_m2);

            // usage index: visits / (capacity * operating_days)
            $usageIndex = $visits12m / ($capacity * $operatingDays);
            $revenuePerM2 = $rev12m / $areaM2;
            $costRecovery = $maintCost12m > 0 ? ($rev12m / $maintCost12m) : ($rev12m > 0 ? 1.0 : 0.0);

            // 6-month forecast
            $forecast = $this->forecastVenueRevenue($venue, 6);

            $venueMetrics[] = [
                'venue_id' => $venue->id,
                'venue_name' => $venue->name,
                'venue_type' => $venue->venueType?->name,
                'district_name' => $venue->district?->name,
                'capacity' => $capacity,
                'area_m2' => $areaM2,
                'revenue_12m' => round((float) $rev12m, 2),
                'visits_12m' => (int) $visits12m,
                'usage_index' => round($usageIndex, 4),
                'revenue_per_m2' => round($revenuePerM2, 2),
                'maintenance_cost_12m' => round((float) $maintCost12m, 2),
                'cost_recovery' => round($costRecovery, 2),
                'forecast_6m' => $forecast,
            ];
        }

        // Calculate medians
        $usageIndexes = array_column($venueMetrics, 'usage_index');
        $revenues = array_column($venueMetrics, 'revenue_12m');

        $medianUsage = $this->calculateMedian($usageIndexes);
        $medianRevenue = $this->calculateMedian($revenues);

        $classified = [];
        $quadrantCounts = [
            'star' => 0,
            'leakage' => 0,
            'niche' => 0,
            'under_utilized' => 0,
        ];

        $recs = config('analytics.levy.recommendations');

        foreach ($venueMetrics as $metric) {
            $isHighUsage = $metric['usage_index'] >= $medianUsage;
            $isHighRevenue = $metric['revenue_12m'] >= $medianRevenue;

            if ($isHighUsage && $isHighRevenue) {
                $qKey = 'star';
            } elseif ($isHighUsage && !$isHighRevenue) {
                $qKey = 'leakage';
            } elseif (!$isHighUsage && $isHighRevenue) {
                $qKey = 'niche';
            } else {
                $qKey = 'under_utilized';
            }

            $quadrantCounts[$qKey]++;

            $recInfo = $recs[$qKey] ?? [
                'classification' => ucfirst($qKey),
                'title' => ucfirst($qKey),
                'recommendation' => 'Tinjau operasional venue.',
            ];

            $metric['quadrant_key'] = $qKey;
            $metric['classification'] = $recInfo['classification'];
            $metric['classification_title'] = $recInfo['title'];
            $metric['recommendation'] = $recInfo['recommendation'];

            $classified[] = $metric;
        }

        return [
            'median_usage_index' => round($medianUsage, 4),
            'median_revenue_12m' => round($medianRevenue, 2),
            'quadrant_summary' => $quadrantCounts,
            'venues' => $classified,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * 6-month linear revenue trend forecast for a venue.
     */
    public function forecastVenueRevenue(Venue $venue, int $months = 6): array
    {
        $monthlyRevs = $venue->venueRevenues()
            ->orderBy('month', 'asc')
            ->get();

        if ($monthlyRevs->count() < 3) {
            $avg = $monthlyRevs->avg('amount') ?: 0.0;
            $lastDate = $monthlyRevs->max('month') ? Carbon::parse($monthlyRevs->max('month')) : Carbon::now();

            $forecastPoints = [];
            for ($i = 1; $i <= $months; $i++) {
                $nextMonth = (clone $lastDate)->addMonthsNoOverflow($i)->format('Y-m-01');
                $forecastPoints[] = [
                    'month' => $nextMonth,
                    'projected_amount' => round((float) $avg, 2),
                ];
            }
            return [
                'method' => 'Average Fallback',
                'points' => $forecastPoints,
            ];
        }

        $xs = [];
        $ys = [];
        $index = 1;

        foreach ($monthlyRevs as $rev) {
            $xs[] = $index++;
            $ys[] = (float) $rev->amount;
        }

        $regression = new LinearRegression();
        $regression->fit($xs, $ys);

        $lastDate = Carbon::parse($monthlyRevs->last()->month);
        $forecastPoints = [];

        for ($i = 1; $i <= $months; $i++) {
            $targetX = count($xs) + $i;
            $predicted = max(0.0, $regression->predict($targetX));
            $nextMonth = (clone $lastDate)->addMonthsNoOverflow($i)->format('Y-m-01');

            $forecastPoints[] = [
                'month' => $nextMonth,
                'projected_amount' => round($predicted, 2),
            ];
        }

        return [
            'method' => 'OLS Linear Regression',
            'slope' => round($regression->slope(), 2),
            'points' => $forecastPoints,
        ];
    }

    private function calculateMedian(array $numbers): float
    {
        if (empty($numbers)) {
            return 0.0;
        }

        sort($numbers);
        $count = count($numbers);
        $middle = (int) floor($count / 2);

        if ($count % 2 === 1) {
            return (float) $numbers[$middle];
        }

        return ((float) $numbers[$middle - 1] + (float) $numbers[$middle]) / 2.0;
    }
}

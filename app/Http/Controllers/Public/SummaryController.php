<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\VenueRevenue;
use App\Services\Analytics\MaintenanceScoreService;
use App\Services\Privacy\AggregationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SummaryController extends Controller
{
    public function __construct(
        protected AggregationService $aggregationService,
        protected MaintenanceScoreService $maintenanceService
    ) {}

    public function index(): JsonResponse
    {
        $data = Cache::remember('public_summary_kpi', 300, function () {
            $summary = $this->aggregationService->summaryStats();
            $maintenance = $this->maintenanceService->run();

            $cutoff12m = Carbon::now()->subMonths(12)->startOfMonth();
            $totalLevy12m = VenueRevenue::whereHas('venue', fn ($q) => $q->where('state', 'published'))
                ->where('month', '>=', $cutoff12m)
                ->sum('amount');

            return [
                'total_venues' => $summary['total_venues'],
                'total_athletes' => $summary['total_athletes'],
                'total_youth_orgs' => $summary['total_youth_orgs'],
                'total_medals' => $summary['medals']['total'],
                'medals_breakdown' => $summary['medals'],
                'total_levy_12m' => round((float) $totalLevy12m, 2),
                'condition_distribution' => $maintenance['distribution'],
                'dummy_ratio' => $summary['dummy_ratio'],
                'dummy_percentage' => $summary['dummy_percentage'],
                'last_updated' => $summary['last_updated'],
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'cached' => true,
                'last_updated' => $data['last_updated'],
                'dummy_ratio' => $data['dummy_ratio'],
            ],
        ]);
    }
}

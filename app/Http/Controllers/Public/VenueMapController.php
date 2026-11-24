<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueDetailResource;
use App\Http\Resources\VenuePublicResource;
use App\Models\Venue;
use App\Services\Analytics\LevyInsightService;
use App\Services\Analytics\MaintenanceScoreService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VenueMapController extends Controller
{
    public function __construct(
        protected MaintenanceScoreService $maintenanceService,
        protected LevyInsightService $levyService
    ) {}

    /**
     * List all published venues with filters and condition/usage indicators.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Venue::where('state', 'published')
            ->with(['venueType', 'district', 'inspections', 'maintenanceRecords', 'venueUsages']);

        if ($request->filled('type_id')) {
            $query->where('venue_type_id', $request->input('type_id'));
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->input('district_id'));
        }

        $venues = $query->get();

        // Calculate scores and metrics
        $cutoff12m = Carbon::now()->subMonths(12)->startOfMonth();
        $operatingDays = config('analytics.levy.operating_days_per_year', 365);

        $processed = $venues->map(function ($venue) use ($cutoff12m, $operatingDays) {
            $scoreData = $this->maintenanceService->scoreFor($venue);
            $visits12m = $venue->venueUsages
                ->filter(fn ($u) => Carbon::parse($u->month)->gte($cutoff12m))
                ->sum('visits');

            $capacity = max(1, (int) $venue->capacity);
            $usageIndex = round($visits12m / ($capacity * $operatingDays), 4);

            $venue->condition_score = $scoreData['condition_score'];
            $venue->condition_status = $scoreData['status'];
            $venue->usage_index = $usageIndex;

            return $venue;
        });

        // Filter by condition status if requested
        if ($request->filled('status')) {
            $statusFilter = strtolower($request->input('status'));
            $processed = $processed->filter(function ($v) use ($statusFilter) {
                return strtolower($v->condition_status) === $statusFilter;
            })->values();
        }

        return response()->json([
            'data' => VenuePublicResource::collection($processed),
            'meta' => [
                'total' => $processed->count(),
            ],
        ]);
    }

    /**
     * Get single venue detail including full historical series and score breakdown.
     */
    public function show(int $id): JsonResponse
    {
        $venue = Venue::where('state', 'published')
            ->with(['venueType', 'district', 'inspections', 'maintenanceRecords', 'venueUsages', 'venueRevenues'])
            ->findOrFail($id);

        $scoreData = $this->maintenanceService->scoreFor($venue);
        $venue->condition_metrics = $scoreData;

        return response()->json([
            'data' => new VenueDetailResource($venue),
        ]);
    }

    /**
     * Generate geospatial heatmap dataset [lat, lng, intensity].
     */
    public function heatmap(): JsonResponse
    {
        $venues = Venue::where('state', 'published')
            ->with(['venueUsages'])
            ->get();

        $cutoff12m = Carbon::now()->subMonths(12)->startOfMonth();
        $operatingDays = config('analytics.levy.operating_days_per_year', 365);

        $points = [];
        $rawIndexes = [];

        foreach ($venues as $venue) {
            if (!$venue->latitude || !$venue->longitude) {
                continue;
            }

            $visits12m = $venue->venueUsages
                ->filter(fn ($u) => Carbon::parse($u->month)->gte($cutoff12m))
                ->sum('visits');

            $capacity = max(1, (int) $venue->capacity);
            $usageIndex = $visits12m / ($capacity * $operatingDays);
            $rawIndexes[] = $usageIndex;

            $points[] = [
                'lat' => (float) $venue->latitude,
                'lng' => (float) $venue->longitude,
                'name' => $venue->name,
                'usage_index' => $usageIndex,
            ];
        }

        $maxIndex = !empty($rawIndexes) ? max($rawIndexes) : 1.0;
        if ($maxIndex <= 0) {
            $maxIndex = 1.0;
        }

        $heatmapData = array_map(function ($p) use ($maxIndex) {
            // Normalized intensity 0.1 to 1.0
            $intensity = min(1.0, max(0.15, round($p['usage_index'] / $maxIndex, 3)));
            return [
                $p['lat'],
                $p['lng'],
                $intensity,
            ];
        }, $points);

        return response()->json([
            'data' => $heatmapData,
            'meta' => [
                'count' => count($heatmapData),
                'max_usage_index' => round($maxIndex, 4),
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Privacy\AggregationService;
use Illuminate\Http\JsonResponse;

class AthleteStatsController extends Controller
{
    public function __construct(
        protected AggregationService $aggregationService
    ) {}

    /**
     * Get combined athlete aggregates for dashboard charts.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'by_sport' => $this->aggregationService->athletesBySport(),
                'by_district' => $this->aggregationService->athletesByDistrict(),
                'demographics' => $this->aggregationService->athleteDemographics(),
                'by_level' => $this->aggregationService->athletesByLevel(),
            ],
            'meta' => [
                'privacy_notice' => 'Semua data atlet yang disajikan merupakan data agregat publik sesuai UU PDP No. 27/2022. Nilai sel di bawah 5 disupresi (< 5).',
                'k_anonymity_min' => config('analytics.k_anonymity_min', 5),
            ],
        ]);
    }

    public function bySport(): JsonResponse
    {
        return response()->json([
            'data' => $this->aggregationService->athletesBySport(),
        ]);
    }

    public function byDistrict(): JsonResponse
    {
        return response()->json([
            'data' => $this->aggregationService->athletesByDistrict(),
        ]);
    }

    public function demographics(): JsonResponse
    {
        return response()->json([
            'data' => $this->aggregationService->athleteDemographics(),
        ]);
    }

    public function byLevel(): JsonResponse
    {
        return response()->json([
            'data' => $this->aggregationService->athletesByLevel(),
        ]);
    }
}

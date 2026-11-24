<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Privacy\AggregationService;
use Illuminate\Http\JsonResponse;

class MedalStatsController extends Controller
{
    public function __construct(
        protected AggregationService $aggregationService
    ) {}

    public function index(): JsonResponse
    {
        $aggregates = $this->aggregationService->medalAggregates();

        return response()->json([
            'data' => $aggregates,
        ]);
    }

    public function byEvent(): JsonResponse
    {
        $aggregates = $this->aggregationService->medalAggregates();

        return response()->json([
            'data' => $aggregates['by_event'],
        ]);
    }

    public function bySport(): JsonResponse
    {
        $aggregates = $this->aggregationService->medalAggregates();

        return response()->json([
            'data' => $aggregates['by_sport'],
        ]);
    }
}

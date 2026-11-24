<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Analytics\SimulationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    public function __construct(
        protected SimulationService $simulationService
    ) {}

    /**
     * Simulate coaching budget adjustment for a sport.
     * POST /public/simulate/budget
     */
    public function simulateBudget(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sport_id' => 'required|integer|exists:sports,id',
            'change_pct' => 'required|numeric|min:-100|max:500',
        ]);

        $result = $this->simulationService->simulateBudget(
            (int) $validated['sport_id'],
            (float) $validated['change_pct']
        );

        return response()->json([
            'data' => $result,
        ]);
    }

    /**
     * Simulate renovation for a venue.
     * POST /public/simulate/renovation
     */
    public function simulateRenovation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'venue_id' => 'required|integer|exists:venues,id',
            'target_score' => 'required|numeric|min:0|max:100',
        ]);

        $result = $this->simulationService->simulateRenovation(
            (int) $validated['venue_id'],
            (float) $validated['target_score']
        );

        return response()->json([
            'data' => $result,
        ]);
    }
}

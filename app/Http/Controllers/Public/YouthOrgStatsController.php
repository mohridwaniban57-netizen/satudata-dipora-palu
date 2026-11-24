<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\YouthOrg;
use App\Services\Privacy\AggregationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class YouthOrgStatsController extends Controller
{
    public function __construct(
        protected AggregationService $aggregationService
    ) {}

    public function index(): JsonResponse
    {
        $byDistrict = $this->aggregationService->youthOrgsByDistrict();

        $byActivity = YouthOrg::where('state', 'published')
            ->select('activity_level', DB::raw('count(*) as count'), DB::raw('sum(members) as total_members'))
            ->groupBy('activity_level')
            ->get();

        $byType = YouthOrg::where('state', 'published')
            ->select('type', DB::raw('count(*) as count'), DB::raw('sum(members) as total_members'))
            ->groupBy('type')
            ->get();

        return response()->json([
            'data' => [
                'by_district' => $byDistrict,
                'by_activity' => $byActivity,
                'by_type' => $byType,
            ],
        ]);
    }

    public function byDistrict(): JsonResponse
    {
        return response()->json([
            'data' => $this->aggregationService->youthOrgsByDistrict(),
        ]);
    }
}

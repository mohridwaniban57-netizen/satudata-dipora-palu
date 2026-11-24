<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Budget;
use App\Models\MedalHistory;
use App\Models\Venue;
use App\Models\YouthOrg;
use Illuminate\Http\JsonResponse;

class DataQualityController extends Controller
{
    public function index(): JsonResponse
    {
        // 1. Venue quality
        $totalVenues = Venue::count();
        $venueDrafts = Venue::where('state', 'draft')->count();
        $venueValidated = Venue::where('state', 'validated')->count();
        $venuePublished = Venue::where('state', 'published')->count();
        $venueDummy = Venue::where('is_dummy', true)->count();
        $venueComplete = Venue::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotNull('address')
            ->where('area_m2', '>', 0)
            ->where('capacity', '>', 0)
            ->count();
        $venueCompleteness = $totalVenues > 0 ? round(($venueComplete / $totalVenues) * 100, 1) : 100.0;

        // 2. Athlete quality
        $totalAthletes = Athlete::count();
        $athleteDrafts = Athlete::where('state', 'draft')->count();
        $athleteValidated = Athlete::where('state', 'validated')->count();
        $athletePublished = Athlete::where('state', 'published')->count();
        $athleteDummy = Athlete::where('is_dummy', true)->count();
        $athleteComplete = Athlete::whereNotNull('sport_id')
            ->whereNotNull('district_id')
            ->whereNotNull('birth_year')
            ->whereNotNull('gender')
            ->count();
        $athleteCompleteness = $totalAthletes > 0 ? round(($athleteComplete / $totalAthletes) * 100, 1) : 100.0;

        // 3. Medal quality
        $totalMedals = MedalHistory::count();
        $medalDrafts = MedalHistory::where('state', 'draft')->count();
        $medalValidated = MedalHistory::where('state', 'validated')->count();
        $medalPublished = MedalHistory::where('state', 'published')->count();
        $medalDummy = MedalHistory::where('is_dummy', true)->count();
        $medalCompleteness = 100.0; // Required schema fields

        // 4. Budget quality
        $totalBudgets = Budget::count();
        $budgetDrafts = Budget::where('state', 'draft')->count();
        $budgetValidated = Budget::where('state', 'validated')->count();
        $budgetPublished = Budget::where('state', 'published')->count();
        $budgetDummy = Budget::where('is_dummy', true)->count();

        // 5. Youth Org quality
        $totalYouth = YouthOrg::count();
        $youthDrafts = YouthOrg::where('state', 'draft')->count();
        $youthValidated = YouthOrg::where('state', 'validated')->count();
        $youthPublished = YouthOrg::where('state', 'published')->count();
        $youthDummy = YouthOrg::where('is_dummy', true)->count();
        $youthComplete = YouthOrg::whereNotNull('district_id')->where('members', '>', 0)->count();
        $youthCompleteness = $totalYouth > 0 ? round(($youthComplete / $totalYouth) * 100, 1) : 100.0;

        // Overall stats
        $allTotal = $totalVenues + $totalAthletes + $totalMedals + $totalBudgets + $totalYouth;
        $allDummy = $venueDummy + $athleteDummy + $medalDummy + $budgetDummy + $youthDummy;
        $allDrafts = $venueDrafts + $athleteDrafts + $medalDrafts + $budgetDrafts + $youthDrafts;
        $overallDummyPct = $allTotal > 0 ? round(($allDummy / $allTotal) * 100, 1) : 0.0;

        return response()->json([
            'data' => [
                'overall' => [
                    'total_records' => $allTotal,
                    'total_dummy' => $allDummy,
                    'dummy_percentage' => $overallDummyPct,
                    'total_drafts' => $allDrafts,
                ],
                'entities' => [
                    'venues' => [
                        'total' => $totalVenues,
                        'draft' => $venueDrafts,
                        'validated' => $venueValidated,
                        'published' => $venuePublished,
                        'dummy' => $venueDummy,
                        'dummy_pct' => $totalVenues > 0 ? round(($venueDummy / $totalVenues) * 100, 1) : 0,
                        'completeness_pct' => $venueCompleteness,
                    ],
                    'athletes' => [
                        'total' => $totalAthletes,
                        'draft' => $athleteDrafts,
                        'validated' => $athleteValidated,
                        'published' => $athletePublished,
                        'dummy' => $athleteDummy,
                        'dummy_pct' => $totalAthletes > 0 ? round(($athleteDummy / $totalAthletes) * 100, 1) : 0,
                        'completeness_pct' => $athleteCompleteness,
                    ],
                    'medals' => [
                        'total' => $totalMedals,
                        'draft' => $medalDrafts,
                        'validated' => $medalValidated,
                        'published' => $medalPublished,
                        'dummy' => $medalDummy,
                        'dummy_pct' => $totalMedals > 0 ? round(($medalDummy / $totalMedals) * 100, 1) : 0,
                        'completeness_pct' => $medalCompleteness,
                    ],
                    'budgets' => [
                        'total' => $totalBudgets,
                        'draft' => $budgetDrafts,
                        'validated' => $budgetValidated,
                        'published' => $budgetPublished,
                        'dummy' => $budgetDummy,
                        'dummy_pct' => $totalBudgets > 0 ? round(($budgetDummy / $totalBudgets) * 100, 1) : 0,
                        'completeness_pct' => 100.0,
                    ],
                    'youth_orgs' => [
                        'total' => $totalYouth,
                        'draft' => $youthDrafts,
                        'validated' => $youthValidated,
                        'published' => $youthPublished,
                        'dummy' => $youthDummy,
                        'dummy_pct' => $totalYouth > 0 ? round(($youthDummy / $totalYouth) * 100, 1) : 0,
                        'completeness_pct' => $youthCompleteness,
                    ],
                ],
            ],
        ]);
    }
}

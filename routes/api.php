<?php

use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\AthleteController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\DataQualityController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\InspectionController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\MedalController;
use App\Http\Controllers\Admin\PredictionController as AdminPredictionController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\SportController;
use App\Http\Controllers\Admin\TransitionController;
use App\Http\Controllers\Admin\UsageController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\YouthOrgController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\AthleteStatsController;
use App\Http\Controllers\Public\MedalStatsController;
use App\Http\Controllers\Public\MethodologyController;
use App\Http\Controllers\Public\PredictionController as PublicPredictionController;
use App\Http\Controllers\Public\SimulationController;
use App\Http\Controllers\Public\SummaryController;
use App\Http\Controllers\Public\VenueMapController;
use App\Http\Controllers\Public\YouthOrgStatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/admin/me', [AuthController::class, 'me']);
});

/*
|--------------------------------------------------------------------------
| Public Routes (Read-only / Aggregates / Simulations)
|--------------------------------------------------------------------------
| Throttle 60 requests per minute
*/
Route::prefix('public')->middleware('throttle:60,1')->group(function () {
    // Executive summary KPI
    Route::get('/summary', [SummaryController::class, 'index']);

    // Geospatial venues & map
    Route::get('/venues', [VenueMapController::class, 'index']);
    Route::get('/venues/{id}', [VenueMapController::class, 'show']);
    Route::get('/heatmap', [VenueMapController::class, 'heatmap']);

    // Aggregates with K-anonymity suppression
    Route::get('/stats/athletes', [AthleteStatsController::class, 'index']);
    Route::get('/stats/athletes/by-sport', [AthleteStatsController::class, 'bySport']);
    Route::get('/stats/athletes/by-district', [AthleteStatsController::class, 'byDistrict']);
    Route::get('/stats/athletes/demographics', [AthleteStatsController::class, 'demographics']);
    Route::get('/stats/athletes/by-level', [AthleteStatsController::class, 'byLevel']);

    // Medal history stats
    Route::get('/stats/medals', [MedalStatsController::class, 'index']);
    Route::get('/stats/medals/by-event', [MedalStatsController::class, 'byEvent']);
    Route::get('/stats/medals/by-sport', [MedalStatsController::class, 'bySport']);

    // Youth organizations stats
    Route::get('/stats/youth-orgs', [YouthOrgStatsController::class, 'index']);
    Route::get('/stats/youth-orgs/by-district', [YouthOrgStatsController::class, 'byDistrict']);

    // Prediction snapshot queries
    Route::get('/predictions/medals', [PublicPredictionController::class, 'medals']);
    Route::get('/predictions/maintenance', [PublicPredictionController::class, 'maintenance']);
    Route::get('/predictions/levy', [PublicPredictionController::class, 'levy']);

    // What-If Simulations
    Route::post('/simulate/budget', [SimulationController::class, 'simulateBudget']);
    Route::post('/simulate/renovation', [SimulationController::class, 'simulateRenovation']);

    // Methodology & metadata
    Route::get('/methodology', [MethodologyController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected by auth:sanctum)
|--------------------------------------------------------------------------
| Throttle 120 requests per minute
*/
Route::prefix('admin')->middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    // Venue Domain
    Route::apiResource('venues', VenueController::class);
    Route::apiResource('inspections', InspectionController::class);
    Route::apiResource('maintenance', MaintenanceController::class);
    Route::apiResource('usage', UsageController::class);
    Route::apiResource('revenue', RevenueController::class);

    // Athlete Domain
    Route::apiResource('athletes', AthleteController::class);
    Route::apiResource('achievements', AchievementController::class);

    // Other Masters
    Route::apiResource('medals', MedalController::class);
    Route::apiResource('budgets', BudgetController::class);
    Route::apiResource('youth-orgs', YouthOrgController::class);
    Route::apiResource('sports', SportController::class);

    // Districts & Venue Types
    Route::get('districts', [DistrictController::class, 'index']);
    Route::get('districts/{id}', [DistrictController::class, 'show']);
    Route::put('districts/{id}', [DistrictController::class, 'update']);
    Route::get('venue-types', [DistrictController::class, 'venueTypes']);

    // Record Lifecycle Transition (draft -> validated -> published)
    Route::post('{entity}/{id}/transition', [TransitionController::class, 'transition']);

    // CSV Import (validate & commit)
    Route::post('import/{dataset}/validate', [ImportController::class, 'validateImport']);
    Route::post('import/{dataset}/commit', [ImportController::class, 'commitImport']);

    // Analytics Engine Recalculation & Snapshot Persistence
    Route::get('predictions', [AdminPredictionController::class, 'index']);
    Route::post('predictions/run', [AdminPredictionController::class, 'run']);

    // Audit Log & Data Quality
    Route::get('audit-logs', [AuditController::class, 'index']);
    Route::get('data-quality', [DataQualityController::class, 'index']);
});

<?php

namespace Tests\Unit;

use App\Models\MedalHistory;
use App\Models\Sport;
use App\Models\Venue;
use App\Services\Analytics\LevyInsightService;
use App\Services\Analytics\LinearRegression;
use App\Services\Analytics\MaintenanceScoreService;
use App\Services\Analytics\MedalProjectionService;
use App\Services\Analytics\SimulationService;
use Tests\TestCase;

class AnalyticsServicesTest extends TestCase
{
    public function test_medal_projection_service_runs_and_returns_projections(): void
    {
        $regression = new LinearRegression();
        $service = new MedalProjectionService($regression);

        $result = $service->run(2026);

        $this->assertArrayHasKey('target_year', $result);
        $this->assertEquals(2026, $result['target_year']);
        $this->assertArrayHasKey('total_projected_points', $result);
        $this->assertArrayHasKey('total_projected_gold', $result);
        $this->assertNotEmpty($result['projections']);

        $first = $result['projections'][0];
        $this->assertArrayHasKey('sport_name', $first);
        $this->assertArrayHasKey('projected_points', $first);
        $this->assertArrayHasKey('points_range_min', $first);
        $this->assertArrayHasKey('points_range_max', $first);
        $this->assertContains($first['confidence_label'], ['Low', 'Medium', 'High']);
    }

    public function test_maintenance_score_service_calculates_scores_and_status(): void
    {
        $service = new MaintenanceScoreService();
        $result = $service->run();

        $this->assertArrayHasKey('venues', $result);
        $this->assertArrayHasKey('distribution', $result);
        $this->assertNotEmpty($result['venues']);

        $venue = $result['venues'][0];
        $this->assertArrayHasKey('condition_score', $venue);
        $this->assertArrayHasKey('risk_score', $venue);
        $this->assertArrayHasKey('status', $venue);
        $this->assertContains($venue['status'], ['Good', 'Watch', 'Critical']);
        $this->assertArrayHasKey('next_maintenance_date', $venue);
        $this->assertEquals(1, $venue['priority_rank']);
    }

    public function test_levy_insight_service_matrix_classification(): void
    {
        $regression = new LinearRegression();
        $service = new LevyInsightService($regression);

        $result = $service->run();

        $this->assertArrayHasKey('median_usage_index', $result);
        $this->assertArrayHasKey('median_revenue_12m', $result);
        $this->assertArrayHasKey('quadrant_summary', $result);
        $this->assertNotEmpty($result['venues']);

        $first = $result['venues'][0];
        $this->assertContains($first['quadrant_key'], ['star', 'leakage', 'niche', 'under_utilized']);
        $this->assertNotEmpty($first['recommendation']);
        $this->assertArrayHasKey('forecast_6m', $first);
        $this->assertCount(6, $first['forecast_6m']['points']);
    }

    public function test_simulation_service_budget_and_renovation(): void
    {
        $regression = new LinearRegression();
        $medalService = new MedalProjectionService($regression);
        $maintenanceService = new MaintenanceScoreService();
        $simulationService = new SimulationService($medalService, $maintenanceService, $regression);

        $sport = Sport::first();
        $budgetSim = $simulationService->simulateBudget($sport->id, 25.0);

        $this->assertEquals(25.0, $budgetSim['change_pct']);
        $this->assertGreaterThan(0, $budgetSim['budget']['simulated']);
        $this->assertArrayHasKey('beta_coefficient', $budgetSim);

        $venue = Venue::where('state', 'published')->first();
        $renovSim = $simulationService->simulateRenovation($venue->id, 98.0);

        $this->assertEquals(98.0, $renovSim['target_inspection_score']);
        $this->assertArrayHasKey('condition_score', $renovSim);
        $this->assertArrayHasKey('status', $renovSim);
        $this->assertArrayHasKey('projected_usage_uplift_pct', $renovSim);
    }
}

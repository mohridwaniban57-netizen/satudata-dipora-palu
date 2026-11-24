<?php

namespace Tests\Feature;

use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    public function test_public_summary_returns_kpi_cards(): void
    {
        $response = $this->getJson('/api/public/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_venues',
                    'total_athletes',
                    'total_youth_orgs',
                    'total_medals',
                    'total_levy_12m',
                    'condition_distribution' => [
                        'good_count',
                        'watch_count',
                        'critical_count',
                    ],
                    'dummy_ratio',
                    'dummy_percentage',
                    'last_updated',
                ],
                'meta',
            ]);
    }

    public function test_public_venues_returns_list_with_metrics(): void
    {
        $response = $this->getJson('/api/public/venues');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'type',
                        'district',
                        'latitude',
                        'longitude',
                        'condition_score',
                        'status',
                        'usage_index',
                    ],
                ],
            ]);
    }

    public function test_public_heatmap_returns_coordinate_tuples(): void
    {
        $response = $this->getJson('/api/public/heatmap');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [0, 1, 2], // [lat, lng, intensity]
                ],
                'meta' => ['count', 'max_usage_index'],
            ]);
    }

    public function test_public_athlete_stats_never_exposes_names_and_suppresses_small_cells(): void
    {
        $response = $this->getJson('/api/public/stats/athletes');

        $response->assertStatus(200);

        $json = $response->json();
        $rawContent = $response->getContent();

        // Data protection assertion: personal names and encrypted names must never be in response
        $this->assertStringNotContainsString('name_encrypted', $rawContent);

        // Assert structure
        $this->assertArrayHasKey('by_sport', $json['data']);
        $this->assertArrayHasKey('by_district', $json['data']);
        $this->assertArrayHasKey('demographics', $json['data']);
        $this->assertArrayHasKey('by_level', $json['data']);

        // Check if any count between 1 and 4 was turned to "< 5"
        foreach ($json['data']['by_sport'] as $item) {
            if ($item['is_suppressed']) {
                $this->assertEquals('< 5', $item['count']);
            }
        }
    }

    public function test_public_predictions_return_persisted_or_computed_data(): void
    {
        $medals = $this->getJson('/api/public/predictions/medals');
        $medals->assertStatus(200)->assertJsonStructure(['data', 'meta']);

        $maintenance = $this->getJson('/api/public/predictions/maintenance');
        $maintenance->assertStatus(200)->assertJsonStructure(['data', 'meta']);

        $levy = $this->getJson('/api/public/predictions/levy');
        $levy->assertStatus(200)->assertJsonStructure(['data', 'meta']);
    }

    public function test_public_simulation_endpoints(): void
    {
        $sport = Sport::first();
        $this->assertNotNull($sport);

        $simBudget = $this->postJson('/api/public/simulate/budget', [
            'sport_id' => $sport->id,
            'change_pct' => 20,
        ]);

        $simBudget->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'sport_id',
                    'change_pct',
                    'budget' => ['baseline', 'delta', 'simulated'],
                    'points' => ['baseline', 'delta', 'simulated'],
                    'explanation',
                ],
            ]);

        $venue = Venue::where('state', 'published')->first();
        $this->assertNotNull($venue);

        $simRenov = $this->postJson('/api/public/simulate/renovation', [
            'venue_id' => $venue->id,
            'target_score' => 95,
        ]);

        $simRenov->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'venue_id',
                    'condition_score' => ['baseline', 'delta', 'simulated'],
                    'status' => ['baseline', 'simulated'],
                    'projected_usage_uplift_pct',
                    'explanation',
                ],
            ]);
    }

    public function test_public_methodology_returns_documentation(): void
    {
        $response = $this->getJson('/api/public/methodology');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'models' => [
                        'medal_projection',
                        'predictive_maintenance',
                        'levy_optimization',
                        'policy_simulation',
                    ],
                    'privacy_framework',
                    'data_sources',
                ],
            ]);
    }
}

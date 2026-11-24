<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndAdminTest extends TestCase
{
    public function test_admin_can_login_and_receive_sanctum_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@dispora-palu.go.id',
            'password' => 'Admin@12345',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email'],
                ],
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@dispora-palu.go.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_me_endpoint_requires_auth(): void
    {
        $unauth = $this->getJson('/api/admin/me');
        $unauth->assertStatus(401);

        $user = User::where('email', 'admin@dispora-palu.go.id')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $auth = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/admin/me');

        $auth->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'admin@dispora-palu.go.id',
                ],
            ]);
    }

    public function test_admin_transition_endpoint(): void
    {
        $user = User::where('email', 'admin@dispora-palu.go.id')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $venue = Venue::first();
        $this->assertNotNull($venue);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/admin/venues/{$venue->id}/transition", [
                'to' => 'published',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'entity' => 'venues',
                    'id' => $venue->id,
                    'to_state' => 'published',
                ],
            ]);
    }

    public function test_admin_data_quality_endpoint(): void
    {
        $user = User::where('email', 'admin@dispora-palu.go.id')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/admin/data-quality');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'overall' => ['total_records', 'total_dummy', 'dummy_percentage', 'total_drafts'],
                    'entities' => ['venues', 'athletes', 'medals', 'budgets', 'youth_orgs'],
                ],
            ]);
    }
}

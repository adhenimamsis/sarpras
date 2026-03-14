<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Health\HealthCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_ok_status_for_healthy_application(): void
    {
        $response = $this->getJson('/health');

        $response
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonStructure([
                'status',
                'timestamp',
            ]);
    }

    public function test_health_endpoint_returns_service_unavailable_when_dependency_fails(): void
    {
        $this->mock(HealthCheckService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('run')
                ->once()
                ->andReturn([
                    'status' => 'degraded',
                    'timestamp' => now()->toIso8601String(),
                    'checks' => [
                        'app' => ['status' => 'ok', 'message' => 'App is booted'],
                        'database' => ['status' => 'fail', 'message' => 'Database connection failed'],
                        'cache' => ['status' => 'ok', 'message' => 'Cache read/write is available'],
                        'storage' => ['status' => 'ok', 'message' => 'Storage directory is writable'],
                    ],
                ]);
        });

        $this->getJson('/health')
            ->assertStatus(503)
            ->assertJsonPath('status', 'degraded');
    }

    public function test_admin_can_see_detailed_health_checks(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->getJson('/health')
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'timestamp',
                'checks' => [
                    'app' => ['status', 'message'],
                    'database' => ['status', 'message'],
                    'cache' => ['status', 'message'],
                    'storage' => ['status', 'message'],
                ],
            ]);
    }
}

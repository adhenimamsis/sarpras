<?php

namespace Tests\Feature\Console;

use App\Support\Health\HealthCheckService;
use Mockery\MockInterface;
use Tests\TestCase;

class OpsDoctorCommandTest extends TestCase
{
    public function test_ops_doctor_json_command_succeeds_when_application_is_healthy(): void
    {
        $this->mock(HealthCheckService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('run')
                ->once()
                ->andReturn([
                    'status' => 'ok',
                    'timestamp' => now()->toIso8601String(),
                    'checks' => [
                        'app' => ['status' => 'ok', 'message' => 'App is booted'],
                        'database' => ['status' => 'ok', 'message' => 'Database connection is available'],
                        'cache' => ['status' => 'ok', 'message' => 'Cache read/write is available'],
                        'storage' => ['status' => 'ok', 'message' => 'Storage directory is writable'],
                    ],
                ]);
        });

        $this->artisan('ops:doctor --json')
            ->expectsOutputToContain('"status": "ok"')
            ->assertSuccessful();
    }

    public function test_ops_doctor_json_command_fails_when_application_is_degraded(): void
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

        $this->artisan('ops:doctor --json')
            ->expectsOutputToContain('"status": "degraded"')
            ->assertFailed();
    }
}

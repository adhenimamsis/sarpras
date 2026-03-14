<?php

namespace App\Support\Health;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class HealthCheckService
{
    public function run(): array
    {
        $checks = [
            'app' => $this->checkApp(),
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $status = collect($checks)->every(
            fn (array $check): bool => $check['status'] === 'ok'
        ) ? 'ok' : 'degraded';

        return [
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ];
    }

    private function checkApp(): array
    {
        return [
            'status' => 'ok',
            'message' => sprintf('%s is booted', config('app.name', 'Application')),
        ];
    }

    private function checkDatabase(): array
    {
        try {
            DB::select('SELECT 1');

            return [
                'status' => 'ok',
                'message' => 'Database connection is available',
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'fail',
                'message' => 'Database connection failed',
            ];
        }
    }

    private function checkCache(): array
    {
        $cacheKey = 'health-check:'.Str::uuid();

        try {
            Cache::put($cacheKey, 'ok', now()->addSeconds(30));
            $value = Cache::get($cacheKey);
            Cache::forget($cacheKey);

            if ($value !== 'ok') {
                return [
                    'status' => 'fail',
                    'message' => 'Cache read/write validation failed',
                ];
            }

            return [
                'status' => 'ok',
                'message' => 'Cache read/write is available',
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'fail',
                'message' => 'Cache is unavailable',
            ];
        }
    }

    private function checkStorage(): array
    {
        $storageCachePath = storage_path('framework/cache');

        if (! is_dir($storageCachePath)) {
            return [
                'status' => 'fail',
                'message' => 'Storage cache directory is missing',
            ];
        }

        if (! is_writable($storageCachePath)) {
            return [
                'status' => 'fail',
                'message' => 'Storage cache directory is not writable',
            ];
        }

        return [
            'status' => 'ok',
            'message' => 'Storage directory is writable',
        ];
    }
}

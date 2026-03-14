<?php

namespace App\Http\Controllers;

use App\Support\Health\HealthCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckController extends Controller
{
    public function __invoke(Request $request, HealthCheckService $healthCheckService): JsonResponse
    {
        $report = $healthCheckService->run();

        $statusCode = $report['status'] === 'ok'
            ? Response::HTTP_OK
            : Response::HTTP_SERVICE_UNAVAILABLE;

        // Public payload dibuat minimal agar endpoint health tidak membocorkan detail internal.
        if (! $request->user()?->isAdmin()) {
            return response()->json([
                'status' => $report['status'],
                'timestamp' => $report['timestamp'],
            ], $statusCode);
        }

        return response()->json($report, $statusCode);
    }
}

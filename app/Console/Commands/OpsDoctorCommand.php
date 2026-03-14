<?php

namespace App\Console\Commands;

use App\Support\Health\HealthCheckService;
use Illuminate\Console\Command;

class OpsDoctorCommand extends Command
{
    protected $signature = 'ops:doctor {--json : Output health report as JSON}';

    protected $description = 'Run operational health checks for the application runtime';

    public function __construct(
        private readonly HealthCheckService $healthCheckService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $report = $this->healthCheckService->run();

        if ($this->option('json')) {
            $this->line((string) json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return $report['status'] === 'ok' ? self::SUCCESS : self::FAILURE;
        }

        $this->components->twoColumnDetail('Overall Status', strtoupper((string) $report['status']));
        $this->newLine();

        $rows = [];
        foreach ($report['checks'] as $name => $check) {
            $rows[] = [
                $name,
                strtoupper((string) ($check['status'] ?? 'unknown')),
                $check['message'] ?? '-',
            ];
        }

        $this->table(['Check', 'Status', 'Message'], $rows);

        if ($report['status'] !== 'ok') {
            $this->components->error('One or more operational checks are failing.');

            return self::FAILURE;
        }

        $this->components->info('All operational checks passed.');

        return self::SUCCESS;
    }
}

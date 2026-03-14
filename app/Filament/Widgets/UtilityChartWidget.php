<?php

namespace App\Filament\Widgets;

use App\Models\UtilityLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UtilityChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Monitoring Konsumsi Utilitas Terpadu';

    protected static string $color = 'info';

    protected static ?int $sort = 2;

    // Ukuran widget full agar grafik detail
    protected int|string|array $columnSpan = 'full';

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 Hari Terakhir',
            '14' => '14 Hari Terakhir',
            '30' => '30 Hari Terakhir',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? '7';
        $limit = (int) $activeFilter;
        $startDate = now()->subDays($limit - 1)->startOfDay();

        // 1. Generate Labe & Range Tanggal (Hanya Sekali)
        $labels = [];
        $dateRange = [];
        for ($i = $limit - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->translatedFormat('d M');
            $dateRange[] = $date->format('Y-m-d');
        }

        // 2. Ambil semua data dalam satu Query (High Performance)
        $allLogs = UtilityLog::where('tgl_catat', '>=', $startDate)
            ->select('jenis', 'nama_meteran', 'angka_meteran', DB::raw('DATE(tgl_catat) as date_only'))
            ->orderBy('tgl_catat', 'asc')
            ->get();

        // Grouping di level Koleksi (bukan Database) agar cepat
        $groupedLogs = $allLogs->groupBy(['jenis', 'date_only']);

        $datasets = [];
        $palette = [
            'listrik' => ['#fbbf24', '#f59e0b', '#d97706'], // Amber
            'air' => '#0ea5e9', // Sky
            'solar' => '#ef4444', // Red
        ];

        // 3. Proses Dataset Listrik (Per Meteran)
        $meterans = $allLogs->where('jenis', 'listrik')->pluck('nama_meteran')->unique();

        foreach ($meterans as $index => $meteran) {
            $dataPoints = [];
            foreach ($dateRange as $date) {
                // Cari angka meteran terakhir pada hari tersebut untuk meteran ini
                $val = $allLogs->where('jenis', 'listrik')
                    ->where('nama_meteran', $meteran)
                    ->where('date_only', $date)
                    ->last()?->angka_meteran ?? 0;
                $dataPoints[] = $val;
            }

            $color = $palette['listrik'][$index % count($palette['listrik'])];
            $datasets[] = [
                'label' => "Listrik: $meteran (kWh)",
                'data' => $dataPoints,
                'borderColor' => $color,
                'backgroundColor' => $color.'22',
                'fill' => false,
                'tension' => 0.3,
                'pointRadius' => 4,
            ];
        }

        // 4. Proses Dataset Air (Akumulasi per hari)
        $airPoints = [];
        foreach ($dateRange as $date) {
            $airPoints[] = $allLogs->where('jenis', 'air')
                ->where('date_only', $date)
                ->sum('angka_meteran');
        }

        $datasets[] = [
            'label' => 'Konsumsi Air Bersih (m³)',
            'data' => $airPoints,
            'borderColor' => $palette['air'],
            'backgroundColor' => $palette['air'].'33',
            'fill' => 'origin',
            'tension' => 0.3,
        ];

        // 5. Proses Dataset Solar (Level Terakhir)
        $solarPoints = [];
        foreach ($dateRange as $date) {
            $solarPoints[] = $allLogs->where('jenis', 'solar')
                ->where('date_only', $date)
                ->last()?->angka_meteran ?? 0;
        }

        $datasets[] = [
            'label' => 'Stok Solar Genset (L)',
            'data' => $solarPoints,
            'borderColor' => $palette['solar'],
            'borderDash' => [5, 5],
            'fill' => false,
            'tension' => 0, // Solar biasanya kaku (step)
        ];

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                        'drawBorder' => false,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}

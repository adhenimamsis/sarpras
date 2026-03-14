<?php

namespace App\Filament\Widgets;

use App\Models\LaporanKerusakan;
use App\Models\MonitoringMfk;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LaporanChart extends ChartWidget
{
    protected static ?string $heading = 'Analisis Beban Kerja & Tren Gangguan';

    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 3;

    /**
     * Menambahkan filter tahun dinamis.
     * Memudahkan Bos untuk audit data tahun-tahun sebelumnya.
     */
    protected function getFilters(): ?array
    {
        $year = (int) date('Y');

        return [
            $year => (string) $year,
            $year - 1 => (string) ($year - 1),
            $year - 2 => (string) ($year - 2),
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? date('Y');

        // 1. Ambil Data Laporan Kerusakan Alat (Data Pasif/Keluhan)
        $kerusakanData = LaporanKerusakan::select(
            DB::raw('count(*) as count'),
            DB::raw('MONTH(tgl_lapor) as month')
        )
            ->whereYear('tgl_lapor', $activeFilter)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // 2. Ambil Data Gangguan Utilitas MFK (Data Aktif/Temuan Lapangan)
        $utilitasData = MonitoringMfk::select(
            DB::raw('count(*) as count'),
            DB::raw('MONTH(tgl_cek) as month')
        )
            ->whereYear('tgl_cek', $activeFilter)
            ->whereIn('status', ['Gangguan', 'Perbaikan', 'Temuan'])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Mapping 12 Bulan agar grafik tidak "patah" jika ada bulan kosong
        $valuesKerusakan = [];
        $valuesUtilitas = [];

        for ($i = 1; $i <= 12; $i++) {
            $valuesKerusakan[] = $kerusakanData[$i] ?? 0;
            $valuesUtilitas[] = $utilitasData[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Kerusakan Alat (Aduan Ruangan)',
                    'data' => $valuesKerusakan,
                    'backgroundColor' => '#f43f5e', // Rose 500
                    'borderColor' => '#be123c',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Gangguan Utilitas (Temuan MFK)',
                    'data' => $valuesUtilitas,
                    'backgroundColor' => '#38bdf8', // Sky 400
                    'borderColor' => '#0369a1',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Konfigurasi UI Chart.js.
     * Menggunakan Stacked Bar agar terlihat total beban kerja per bulan.
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'stacked' => true,
                    'ticks' => [
                        'precision' => 0,
                        'stepSize' => 1,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Kejadian',
                    ],
                ],
                'x' => [
                    'stacked' => true,
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}

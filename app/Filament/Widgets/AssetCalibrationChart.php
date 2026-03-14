<?php

namespace App\Http\Controllers;

namespace App\Filament\Widgets;

use App\Models\Asset;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AssetCalibrationChart extends ChartWidget
{
    protected static ?string $heading = 'Analisis Kesiapan Alat Medis (MFK)';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    // Refresh data otomatis setiap menit
    protected static ?string $pollingInterval = '60s';

    /**
     * Deskripsi dinamis di bawah judul widget.
     * Memberikan informasi persentase kepatuhan secara real-time.
     */
    public function getDescription(): ?string
    {
        $totalAlkes = Asset::whereNotNull('kategori_kib')->count(); // Fokus pada aset terdaftar
        $terkalibrasi = Asset::where('tgl_kalibrasi_selanjutnya', '>=', Carbon::today())->count();

        $kepatuhan = $totalAlkes > 0 ? round(($terkalibrasi / $totalAlkes) * 100) : 0;

        return "Tingkat kepatuhan kalibrasi alat saat ini: {$kepatuhan}%. Target Akreditasi: 100%.";
    }

    protected function getData(): array
    {
        $hariIni = Carbon::today();

        // 1. Ambil Data dengan Filter Kategori (Hanya Alkes/Alat yang wajib kalibrasi)
        // Kita asumsikan Alkes memiliki penanda tertentu atau tgl_kalibrasi tidak null
        $terkalibrasi = Asset::where('tgl_kalibrasi_selanjutnya', '>=', $hariIni)->count();

        $expired = Asset::where('tgl_kalibrasi_selanjutnya', '<', $hariIni)->count();

        $belumKalibrasi = Asset::whereNull('tgl_kalibrasi_selanjutnya')
            ->whereNotNull('nama_alat') // Memastikan bukan record sampah
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status Kalibrasi',
                    'data' => [$terkalibrasi, $expired, $belumKalibrasi],
                    'backgroundColor' => [
                        '#10b981', // Emerald (Siap Pakai)
                        '#f59e0b', // Amber (Jatuh Tempo/Expired)
                        '#ef4444', // Red (Data Tidak Lengkap)
                    ],
                    'hoverOffset' => 15,
                    'borderWidth' => 3,
                    'borderColor' => 'transparent',
                ],
            ],
            'labels' => [
                'Layak Pakai',
                'Expired',
                'Belum Ada Data',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Tampilan donat lebih elegan untuk proporsi data
    }

    /**
     * Konfigurasi Chart.js untuk Legend dan Tooltips.
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
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'callbacks' => [
                        'label' => "function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            return ' ' + label + ': ' + value + ' Unit';
                        }",
                    ],
                ],
            ],
            'cutout' => '70%', // Membuat lubang tengah lebih besar (Clean Look)
        ];
    }
}

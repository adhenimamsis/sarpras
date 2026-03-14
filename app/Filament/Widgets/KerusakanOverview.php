<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\LaporanKerusakan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KerusakanOverview extends BaseWidget
{
    /**
     * Polling dipercepat untuk kebutuhan Tim Teknis & Sarpras
     * Dashboard akan update otomatis setiap 15 detik.
     */
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // 1. Kalkulasi Indikator Kesiapan Fasilitas (MFK)
        $totalAset = Asset::count() ?: 1;
        $asetBaik = Asset::where('kondisi', 'Baik')->count();
        $persentaseSiap = round(($asetBaik / $totalAset) * 100);

        // 2. Monitoring Beban Kerja & Antrean
        $laporanBaru = LaporanKerusakan::where('status', 'Lapor')->count();
        $dalamProses = LaporanKerusakan::where('status', 'Proses')->count();
        $selesaiBulanIni = LaporanKerusakan::where('status', 'Selesai')
            ->whereMonth('updated_at', now()->month)
            ->count();

        return [
            // STAT 1: KESIAPAN PELAYANAN (KPI Sarpras)
            Stat::make('Kesiapan Alat Pelayanan', "{$persentaseSiap}%")
                ->description("{$asetBaik} dari {$totalAset} alat kondisi prima")
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([85, 88, $persentaseSiap - 5, $persentaseSiap - 2, $persentaseSiap])
                ->color($persentaseSiap > 90 ? 'success' : ($persentaseSiap > 75 ? 'warning' : 'danger')),

            // STAT 2: RESPON CEPAT (Laporan Baru)
            Stat::make('Antrean Laporan Baru', "{$laporanBaru} Kasus")
                ->description($laporanBaru > 0 ? 'Butuh respon segera!' : 'Belum ada laporan masuk')
                ->descriptionIcon($laporanBaru > 0 ? 'heroicon-m-bell-alert' : 'heroicon-m-hand-thumb-up')
                ->chart($laporanBaru > 0 ? [2, 5, 3, 8, $laporanBaru] : [0, 0, 0])
                ->color($laporanBaru > 0 ? 'danger' : 'gray')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    // Navigasi ke tab 'Lapor' di ListLaporanKerusakan
                    'onclick' => "window.location.href='/admin/laporan-kerusakans?activeTab=baru'",
                ]),

            // STAT 3: PROGRESS PERBAIKAN (Maintenance)

            Stat::make('Dalam Perbaikan', "{$dalamProses} Unit")
                ->description("{$selesaiBulanIni} perbaikan selesai bulan ini")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->chart([1, 3, 2, 4, $dalamProses])
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/laporan-kerusakans?activeTab=proses'",
                ]),
        ];
    }
}

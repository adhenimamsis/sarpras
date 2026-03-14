<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\MonitoringMfk;
use App\Models\StokOksigen;
use App\Models\UtilityLog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    /**
     * Dashboard refresh setiap 15 detik (lebih stabil untuk produksi).
     */
    protected static ?string $pollingInterval = '15s';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Cache singkat 10 detik supaya performa server tetap enteng
        return Cache::remember('dashboard_stats_final', 10, function () {
            $now = Carbon::now();
            $today = $now->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();

            // --- 1. KOMPUTASI UTILITAS (LISTRIK) ---
            $listrikHariIni = UtilityLog::where('jenis', 'listrik')->whereDate('tgl_catat', $today)->sum('angka_meteran') ?? 0;
            $listrikKemarin = UtilityLog::where('jenis', 'listrik')->whereDate('tgl_catat', $yesterday)->sum('angka_meteran') ?? 0;

            // Trend 7 hari terakhir
            $utilityTrend = UtilityLog::where('jenis', 'listrik')->latest()->limit(7)->pluck('angka_meteran')->reverse()->toArray();

            $utilityIssues = MonitoringMfk::whereIn('status', ['Gangguan', 'Perbaikan'])
                ->whereDate('tgl_cek', $today)
                ->count();

            // --- 2. KOMPUTASI PROTEKSI KEBAKARAN (APAR) ---
            $aparExpired = Asset::where('nama_alat', 'LIKE', '%APAR%')
                ->where(function ($q) use ($today) {
                    $q->where('tgl_kadaluarsa', '<', $today)
                        ->orWhere('kondisi', 'Rusak Berat');
                })->count();

            // --- 3. KOMPUTASI LOGISTIK KRITIS (O2 & BBM) ---
            $o2Besar = StokOksigen::where('ukuran', '6m3')->latest()->first();
            $o2Kecil = StokOksigen::where('ukuran', '1m3')->latest()->first();
            $solar = UtilityLog::where('jenis', 'solar')->latest()->first();

            $stokSolar = $solar?->angka_meteran ?? 0;
            $stokO2Besar = $o2Besar?->stok_akhir ?? 0;
            $stokO2Kecil = $o2Kecil?->stok_akhir ?? 0;

            $isCritical = ($stokO2Besar < 3 || $stokO2Kecil < 2 || $stokSolar < 15);

            // --- 4. KEPATUHAN & KALIBRASI (STANDAR AKREDITASI) ---
            $alkesQuery = Asset::where('kategori_kib', 'KIB_B');
            $totalAlkes = (clone $alkesQuery)->count();
            $expiredCal = (clone $alkesQuery)->where('tgl_kalibrasi_selanjutnya', '<', $today)->count();

            // Cek Garansi yang akan habis (Early Warning)
            $garansiAkanHabis = Asset::where('tgl_garansi_habis', '>', $today)
                ->where('tgl_garansi_habis', '<=', $now->copy()->addDays(30))
                ->count();

            $complianceRate = $totalAlkes > 0 ? round((($totalAlkes - $expiredCal) / $totalAlkes) * 100) : 0;

            return [
                // KARTU 1: LISTRIK & GANGGUAN MFK
                Stat::make('Status Utilitas', $utilityIssues > 0 ? "⚠️ {$utilityIssues} Masalah" : number_format($listrikHariIni).' kWh')
                    ->description($utilityIssues > 0 ? 'Ada gangguan utilitas aktif!' : ($listrikHariIni > $listrikKemarin ? 'Konsumsi naik' : 'Konsumsi stabil'))
                    ->descriptionIcon($utilityIssues > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-bolt')
                    ->chart($utilityTrend ?: [0, 0, 0])
                    ->color($utilityIssues > 0 ? 'danger' : ($listrikHariIni > $listrikKemarin ? 'warning' : 'success'))
                    ->extraAttributes(['class' => 'cursor-pointer', 'onclick' => "window.location.href='/admin/monitoring-mfks'"]),

                // KARTU 2: SISTEM PEMADAM (APAR)
                Stat::make('Sistem APAR', $aparExpired > 0 ? "🚨 {$aparExpired} Bermasalah" : '✅ Siaga Penuh')
                    ->description($aparExpired > 0 ? 'Cek tabung kadaluarsa/rusak' : 'Semua unit siap digunakan')
                    ->descriptionIcon('heroicon-m-fire')
                    ->color($aparExpired > 0 ? 'danger' : 'success')
                    ->extraAttributes(['class' => 'cursor-pointer', 'onclick' => "window.location.href='/admin/assets?tableFilters[nama_alat][value]=APAR'"]),

                // KARTU 3: STOK OKSIGEN & SOLAR
                Stat::make('Oksigen & Solar', "O2: {$stokO2Besar}/{$stokO2Kecil} | ⛽ {$stokSolar}L")
                    ->description($isCritical ? '⚠️ KRITIS: SEGERA ISI ULANG!' : 'Stok logistik aman')
                    ->descriptionIcon('heroicon-m-truck')
                    ->chart([30, 25, 20, 18, 16, $stokSolar])
                    ->color($isCritical ? 'danger' : 'info'),

                // KARTU 4: STANDAR AKREDITASI
                Stat::make('Kepatuhan MFK', $complianceRate.'%')
                    ->description($garansiAkanHabis > 0 ? "{$garansiAkanHabis} garansi segera habis" : "{$expiredCal} alat butuh kalibrasi")
                    ->descriptionIcon('heroicon-m-shield-check')
                    ->chart([$complianceRate, 90, 95, 100])
                    ->color($complianceRate < 100 || $garansiAkanHabis > 0 ? 'warning' : 'success')
                    ->extraAttributes(['class' => 'cursor-pointer', 'onclick' => "window.location.href='/admin/assets'"]),
            ];
        });
    }
}

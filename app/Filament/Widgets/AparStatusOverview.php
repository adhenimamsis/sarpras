<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AparStatusOverview extends BaseWidget
{
    // Dashboard akan refresh otomatis setiap 30 detik untuk pantauan real-time
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        // 1. Logic: APAR yang sudah kadaluarsa (Expired)
        $expiredAparCount = Asset::where('nama_alat', 'LIKE', '%APAR%')
            ->whereNotNull('tgl_kalibrasi_selanjutnya')
            ->where('tgl_kalibrasi_selanjutnya', '<', $today)
            ->count();

        // 2. Logic: APAR yang akan jatuh tempo dalam 30 hari ke depan (Warning)
        $warningAparCount = Asset::where('nama_alat', 'LIKE', '%APAR%')
            ->whereNotNull('tgl_kalibrasi_selanjutnya')
            ->whereBetween('tgl_kalibrasi_selanjutnya', [
                $today,
                $now->copy()->addDays(30)->format('Y-m-d'),
            ])
            ->count();

        // 3. Logic: Masalah Fisik/Tekanan/Temuan Monitoring
        // Fokus pada kondisi aset yang tidak 'Baik' atau catatan khusus pada tabung
        $lowPressureCount = Asset::where('nama_alat', 'LIKE', '%APAR%')
            ->where(function ($query) {
                $query->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat'])
                    ->orWhere('catatan', 'LIKE', '%Lemah%')
                    ->orWhere('catatan', 'LIKE', '%Low%')
                    ->orWhere('catatan', 'LIKE', '%Tekanan Turun%');
            })
            ->count();

        return [
            // KARTU 1: STATUS KADALUARSA
            Stat::make('APAR Kadaluarsa', $expiredAparCount.' Unit')
                ->description($expiredAparCount > 0 ? 'Segera lakukan pengisian ulang!' : 'Semua unit masih berlaku')
                ->descriptionIcon($expiredAparCount > 0 ? 'heroicon-m-x-circle' : 'heroicon-m-check-badge')
                ->color($expiredAparCount > 0 ? 'danger' : 'success')
                ->chart([5, 10, 8, 12, 7, 15, $expiredAparCount])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/assets?tableFilters[nama_alat][value]=APAR&tableFilters[kondisi][value]=Rusak'",
                ]),

            // KARTU 2: STATUS JATUH TEMPO
            Stat::make('Jatuh Tempo (30 Hari)', $warningAparCount.' Unit')
                ->description('Siapkan anggaran pemeliharaan')
                ->descriptionIcon('heroicon-m-clock')
                ->color($warningAparCount > 0 ? 'warning' : 'gray')
                ->chart([2, 4, 6, 3, 5, 8, $warningAparCount]),

            // KARTU 3: STATUS KONDISI FISIK

            Stat::make('Masalah Tekanan/Fisik', $lowPressureCount.' Unit')
                ->description($lowPressureCount > 0 ? 'Ada unit perlu pengecekan teknis' : 'Tekanan tabung terpantau normal')
                ->descriptionIcon($lowPressureCount > 0 ? 'heroicon-m-fire' : 'heroicon-m-shield-check')
                ->color($lowPressureCount > 0 ? 'danger' : 'success')
                ->chart([1, 0, 2, 1, 0, 3, $lowPressureCount]),
        ];
    }
}

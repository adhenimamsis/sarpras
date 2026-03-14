<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsOverview extends BaseWidget
{
    /**
     * Polling interval untuk refresh data otomatis.
     */
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Menggunakan helper Laravel yang lebih modern untuk format Rupiah
        $formatRupiah = fn ($value) => 'Rp '.number_format($value ?? 0, 0, ',', '.');

        try {
            // Mengambil data agregat dari database
            $totalNilai = Asset::sum('harga_perolehan') ?? 0;
            $totalUnit = Asset::count();

            $kondisiBaik = Asset::whereIn('kondisi', ['Baik', 'B'])->count();
            $rusakBerat = Asset::whereIn('kondisi', ['Rusak Berat', 'RB'])->count();
        } catch (\Exception $e) {
            // Fallback jika database belum siap atau kolom belum ada
            $totalNilai = 0;
            $totalUnit = 0;
            $kondisiBaik = 0;
            $rusakBerat = 0;
        }

        return [
            // KARTU 1: INVESTASI TOTAL
            Stat::make('Total Investasi Aset', $formatRupiah($totalNilai))
                ->description($totalUnit.' Total unit terdaftar')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 3, 5, 2, 10, 4, 12])
                ->color('success'),

            // KARTU 2: KESIAPAN ALAT (KONDISI BAIK)
            Stat::make('Aset Kondisi Baik', $kondisiBaik.' Unit')
                ->description('Siap digunakan untuk pelayanan')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([3, 5, 4, 6, 5, 7, 8])
                ->color('info'),

            // KARTU 3: PERINGATAN KERUSAKAN (RUSAK BERAT)
            Stat::make('Rusak Berat', $rusakBerat.' Unit')
                ->description($rusakBerat > 0 ? 'Butuh penghapusan atau perbaikan' : 'Laporan aset aman')
                ->descriptionIcon($rusakBerat > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-shield-check')
                ->color($rusakBerat > 0 ? 'danger' : 'gray')
                ->chart([10, 2, 8, 3, 5, 2, $rusakBerat])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    // Navigasi otomatis ke halaman asset dengan filter rusak berat.
                    'onclick' => "window.location.href='/admin/assets?tableFilters[kondisi][value]=Rusak%20Berat'",
                ]),
        ];
    }
}

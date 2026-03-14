<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MfkStatusOverview extends BaseWidget
{
    /**
     * Dashboard akan update otomatis setiap 30 detik.
     * Memastikan data yang dilihat Bos selalu yang terbaru.
     */
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // 1. Ambil Data Dasar
        $totalAset = Asset::count();

        // Menggunakan strtolower untuk memastikan data tertangkap meski di DB pake huruf besar/kecil
        $asetBaik = Asset::whereRaw('LOWER(kondisi) = ?', ['baik'])->count();

        $asetBermasalah = Asset::whereIn('kondisi', ['rusak ringan', 'rusak berat', 'Rusak Ringan', 'Rusak Berat'])
            ->count();

        // 2. Kalkulasi Indikator Kinerja Utama (IKU) Sarpras
        $persentaseSiap = $totalAset > 0 ? round(($asetBaik / $totalAset) * 100) : 0;

        return [
            // STAT 1: VOLUME INVENTARIS
            Stat::make('Total Inventaris Aset', number_format($totalAset).' Unit')
                ->description('Seluruh aset terdaftar di SimSarpras')
                ->descriptionIcon('heroicon-m-cube', IconPosition::Before)
                ->chart([$totalAset - 5, $totalAset - 2, $totalAset])
                ->color('info')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/assets'",
                ]),

            // STAT 2: KESIAPAN FASILITAS (MFK)
            Stat::make('Kesiapan Alat (Siap Pakai)', $persentaseSiap.'%')
                ->description($asetBaik.' Unit kondisi prima')
                ->descriptionIcon('heroicon-m-check-badge', IconPosition::Before)
                ->chart([70, 85, 90, $persentaseSiap])
                ->color($persentaseSiap >= 90 ? 'success' : 'warning'),

            // STAT 3: ALERT PERBAIKAN (URGENT)

            Stat::make('Aset Perlu Perbaikan', $asetBermasalah.' Unit')
                ->description($asetBermasalah > 0 ? 'Segera tindak lanjuti perbaikan' : 'Laporan kerusakan nihil')
                ->descriptionIcon($asetBermasalah > 0 ? 'heroicon-m-bell-alert' : 'heroicon-m-shield-check', IconPosition::Before)
                ->chart([$asetBermasalah + 5, $asetBermasalah + 2, $asetBermasalah])
                ->color($asetBermasalah > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/assets?tableFilters[kondisi][values][0]=rusak+ringan&tableFilters[kondisi][values][1]=rusak+berat'",
                ]),
        ];
    }
}

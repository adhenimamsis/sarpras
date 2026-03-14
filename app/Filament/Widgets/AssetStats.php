<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStats extends BaseWidget
{
    /**
     * Dashboard update otomatis setiap 30 detik.
     */
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $now = Carbon::now();

        // 1. Data Dasar & Nilai Aset
        $totalAset = Asset::count();
        $totalNilai = Asset::sum('harga_perolehan') ?? 0;

        // 2. Analisis Kondisi Fisik
        // Menggunakan whereRaw agar 'baik' atau 'Baik' tetap terhitung dengan benar
        $rusakRingan = Asset::whereRaw('LOWER(kondisi) = ?', ['rusak ringan'])->count();
        $rusakBerat = Asset::whereRaw('LOWER(kondisi) = ?', ['rusak berat'])->count();
        $totalBermasalah = $rusakRingan + $rusakBerat;

        // 3. Analisis Kepatuhan MFK (Kalibrasi Alkes)
        $expired = Asset::where('status_kalibrasi', true)
            ->whereNotNull('tgl_kalibrasi_selanjutnya')
            ->where('tgl_kalibrasi_selanjutnya', '<', $now->toDateString())
            ->count();

        // 4. Kalkulasi KPI Kesiapan Fasilitas
        $persentaseSehat = $totalAset > 0 ? round((($totalAset - $totalBermasalah) / $totalAset) * 100) : 0;

        return [
            // KARTU 1: INVESTASI
            Stat::make('Investasi Sarpras', 'Rp '.number_format($totalNilai, 0, ',', '.'))
                ->description($totalAset.' item terdata di Puskesmas')
                ->descriptionIcon('heroicon-m-cube-transparent')
                ->chart([5, 8, 12, 10, 15, 12, 18])
                ->color('info')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/assets'",
                ]),

            // KARTU 2: KESEHATAN ASET (STANDAR PELAYANAN)
            Stat::make('Kesehatan Aset', $persentaseSehat.'%')
                ->description($totalBermasalah > 0 ? $totalBermasalah.' unit butuh perbaikan' : 'Seluruh alat kondisi prima')
                ->descriptionIcon($totalBermasalah > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart([$totalAset, $totalAset - $rusakRingan, $totalAset - $totalBermasalah])
                ->color($persentaseSehat < 90 ? 'danger' : ($persentaseSehat < 100 ? 'warning' : 'success'))
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/assets?tableFilters[kondisi][value]=rusak_berat'",
                ]),

            // KARTU 3: KEPATUHAN KALIBRASI (SIAP AKREDITASI)
            Stat::make('Wajib Kalibrasi', $expired.' Alkes')
                ->description($expired > 0 ? 'Masa berlaku sertifikat habis!' : 'Sertifikat kalibrasi valid')
                ->descriptionIcon($expired > 0 ? 'heroicon-m-bell-alert' : 'heroicon-m-shield-check')
                ->chart([$expired, $expired + 2, $expired + 5, $expired])
                ->color($expired > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    // Navigasi langsung ke filter alkes yang butuh kalibrasi
                    'onclick' => "window.location.href='/admin/assets?tableFilters[status_kalibrasi][value]=1'",
                ]),
        ];
    }
}

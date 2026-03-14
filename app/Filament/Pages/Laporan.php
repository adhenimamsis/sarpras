<?php

namespace App\Filament\Pages;

use App\Models\Asset;
use App\Models\LaporanKerusakan;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class Laporan extends Page
{
    protected static ?string $title = 'Pusat Laporan Sarpras';

    protected static ?string $navigationLabel = 'Laporan Sarpras';

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationGroup = 'Pusat Laporan';

    protected static ?string $slug = 'laporan-sarpras';

    protected static string $view = 'filament.pages.laporan';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return $user->can('reports.view.operational') || $user->can('reports.view.sensitive');
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    /*
    |--------------------------------------------------------------------------
    | LIVEWIRE METHODS (Untuk Tombol di Body Blade)
    |--------------------------------------------------------------------------
    */

    /**
     * Cetak KIB berdasarkan kategori (A, B, C, D, E).
     */
    public function cetakKib($kategori)
    {
        if (! $this->canViewSensitiveReports()) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Role Anda tidak memiliki izin untuk laporan KIB.')
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title("Mencetak KIB $kategori")
            ->body('Dokumen sedang disiapkan...')
            ->success()
            ->send();

        return redirect()->route('cetak.kib', ['kategori' => 'KIB_'.$kategori]);
    }

    /**
     * Cetak Laporan MFK / Bulanan.
     */
    public function cetakMfk()
    {
        if (! $this->canViewSensitiveReports()) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Role Anda tidak memiliki izin untuk laporan MFK terpadu.')
                ->danger()
                ->send();

            return;
        }

        if (! Route::has('laporan.bulanan.pdf')) {
            Notification::make()->title('Rute laporan MFK tidak ditemukan!')->danger()->send();

            return;
        }

        return redirect()->route('laporan.bulanan.pdf');
    }

    /**
     * Cetak Jadwal Kalibrasi Alkes.
     */
    public function cetakKalibrasi()
    {
        if (! $this->canViewSensitiveReports()) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Role Anda tidak memiliki izin untuk laporan kalibrasi.')
                ->danger()
                ->send();

            return;
        }

        Notification::make()->title('Mengunduh Jadwal Kalibrasi')->info()->send();

        return redirect()->route('cetak.kalibrasi.alkes');
    }

    /**
     * Cetak Daftar Legalitas (Sertifikat/IMB).
     */
    public function cetakLegalitas()
    {
        if (! $this->canViewSensitiveReports()) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Role Anda tidak memiliki izin untuk laporan legalitas.')
                ->danger()
                ->send();

            return;
        }

        return redirect()->route('cetak.legalitas.sarpras');
    }

    /**
     * Cetak Laporan Terpadu (Satu PDF untuk semua).
     */
    public function cetakLaporanTerpadu()
    {
        if (! $this->canViewSensitiveReports()) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Role Anda tidak memiliki izin untuk laporan terpadu.')
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title('Laporan Terpadu Digenerate')
            ->body('Harap tunggu, proses ini memakan waktu lebih lama...')
            ->warning()
            ->send();

        return $this->cetakMfk(); // Mengarah ke rute yang sama dengan MFK Terpadu
    }

    /*
    |--------------------------------------------------------------------------
    | FILAMENT ACTIONS (Untuk Tombol di Header Atas)
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetakLaporanHeader')
                ->label('Cetak Ke Kapus')
                ->icon('heroicon-o-printer')
                ->color('danger')
                ->size('lg')
                ->url(fn () => route('laporan.bulanan.pdf'))
                ->openUrlInNewTab()
                ->visible(fn () => Route::has('laporan.bulanan.pdf') && $this->canViewSensitiveReports()),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DATA & STATISTIK (Untuk Sidebar & View)
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = LaporanKerusakan::where('status', 'Lapor')->count();

            return $count > 0 ? (string) $count : 'PDF';
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return LaporanKerusakan::where('status', 'Lapor')->exists() ? 'danger' : 'success';
    }

    protected function getViewData(): array
    {
        $assetQuery = Asset::query();
        $hasKondisi = Schema::hasColumn('assets', 'kondisi');
        $hasHargaPerolehan = Schema::hasColumn('assets', 'harga_perolehan');

        $asetBaik = $hasKondisi
            ? (clone $assetQuery)->whereIn('kondisi', ['Baik', 'B'])->count()
            : 0;

        $asetRusak = $hasKondisi
            ? (clone $assetQuery)->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'RR', 'RB', 'KB'])->count()
            : 0;

        return [
            'stats' => [
                'total_aset' => (clone $assetQuery)->count(),
                'aset_baik' => $asetBaik,
                'aset_rusak' => $asetRusak,
                'kerusakan_aktif' => LaporanKerusakan::where('status', '!=', 'Selesai')->count(),
                'total_investasi' => $hasHargaPerolehan ? (float) (clone $assetQuery)->sum('harga_perolehan') : 0,
            ],
        ];
    }

    private function canViewSensitiveReports(): bool
    {
        return auth()->user()?->can('reports.view.sensitive') ?? false;
    }
}

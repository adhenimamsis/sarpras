<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AssetPublicController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanKerusakanController;
use App\Http\Controllers\MonitoringMfkController;
use App\Http\Controllers\PenghapusanAssetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokOksigenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIM-SARPRAS UPT PUSKESMAS BENDAN
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('throttle:30,1')->get('/health', HealthCheckController::class)->name('health');

/*
 * --- 0. RUTE PUBLIK (QR SCAN) ---
 * Akses informasi aset bagi petugas ruangan tanpa login.
 */
Route::get('/scan/{kode_unik}', [AssetPublicController::class, 'show'])
    ->name('asset.public.show');

/*
 * --- RUTE TERPROTEKSI (WAJIB LOGIN) ---
 */
Route::middleware(['auth', 'verified'])->group(function () {
    // --- 1. DASHBOARD & PROFILE ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // --- 2. MODUL LAPORAN TERPADU (SIMBADA & AKREDITASI) ---
    Route::controller(LaporanController::class)->group(function () {
        // Laporan Komprehensif MFK (Tombol Utama Dashboard)
        Route::middleware('can:reports.view.sensitive')
            ->get('/laporan-bulanan-pdf', 'cetakPdf')
            ->name('laporan.bulanan.pdf');

        // Laporan KIB A-F
        Route::middleware('can:reports.view.sensitive')
            ->get('/cetak-kib/{kategori}', 'cetakKib')
            ->name('cetak.kib');

        // Laporan Kalibrasi & Legalitas
        Route::middleware('can:reports.view.sensitive')
            ->get('/cetak-kalibrasi-alkes', 'cetakKalibrasi')
            ->name('cetak.kalibrasi.alkes');
        Route::middleware('can:reports.view.sensitive')
            ->get('/cetak-legalitas-sarpras', 'cetakLegalitas')
            ->name('cetak.legalitas.sarpras');
    });

    // --- 3. MONITORING MFK & UTILITAS ---
    Route::prefix('monitoring')->group(function () {
        Route::controller(MonitoringMfkController::class)->prefix('mfk')->group(function () {
            // Dashboard Listrik Dinamis
            Route::middleware('can:reports.view.operational')
                ->get('/listrik', 'index')
                ->name('monitoring.listrik');

            // Cetak-mencetak & Export
            Route::middleware('can:reports.view.operational')
                ->get('/print/{record}', 'print')
                ->name('monitoring.mfk.print');
            Route::middleware('can:reports.view.operational')
                ->get('/rekap', 'cetakRekap')
                ->name('monitoring.mfk.rekap');
            Route::middleware('can:reports.export.operational')
                ->get('/export-excel', 'exportExcel')
                ->name('monitoring.mfk.excel');

            // UPDATE: Rute Form Checklist Kosong untuk Lapangan
            Route::middleware('can:reports.view.operational')
                ->get('/form-kosong', 'cetakFormKosong')
                ->name('monitoring.form.kosong');
        });
    });

    // --- 4. MODUL STOK OKSIGEN ---
    Route::prefix('stok-oksigen')->name('cetak.')->group(function () {
        Route::middleware('can:reports.view.operational')
            ->get('/laporan', [StokOksigenController::class, 'cetakLaporan'])
            ->name('stok-oksigen');
    });

    // --- 5. MODUL ASET, RUANGAN & LABEL ---
    // KIR (Kartu Inventaris Ruangan)
    Route::get('/cetak-ruangan/{id}', [CetakController::class, 'ruangan'])->name('cetak.ruangan');

    // Label Barcode Aset
    Route::prefix('asset')->name('asset.')->group(function () {
        Route::get('/print-label/{record}', [CetakController::class, 'assetLabel'])->name('print-label');
        Route::get('/print-bulk/{ids}', [CetakController::class, 'assetBulk'])->name('print-bulk');
    });

    // --- 6. MODUL LAPORAN KERUSAKAN ---
    Route::controller(LaporanKerusakanController::class)->prefix('laporan-kerusakan')->group(function () {
        Route::middleware('can:reports.view.operational')
            ->get('/', 'index')
            ->name('laporan.kerusakan.index');
        Route::middleware('can:reports.view.operational')
            ->get('/print/{record}', 'print')
            ->name('laporan.kerusakan.print');
        Route::middleware('can:reports.view.operational')
            ->get('/cetak-rekap/{status}', 'cetakRekap')
            ->name('cetak.rekap.kerusakan');
    });

    // --- 7. MODUL PENGHAPUSAN ASET ---
    Route::controller(PenghapusanAssetController::class)->prefix('penghapusan')->name('penghapusan.')->group(function () {
        Route::middleware('can:reports.view.sensitive')
            ->get('/print/{record}', 'print')
            ->name('print');
        Route::middleware('can:reports.view.sensitive')
            ->get('/rekap', 'cetakRekap')
            ->name('rekap');
    });

    // --- 8. SHORTCUTS & REDIRECTS FILAMENT ---
    Route::get('/admin-assets-create', fn () => redirect('/admin/assets/create'))->name('assets.create');
    Route::get('/maintenance-log', fn () => redirect('/admin/maintenance-logs'))->name('maintenance.log');
    Route::get('/utility-chart', fn () => redirect('/admin/utility-logs'))->name('utility.chart');
});

require __DIR__.'/auth.php';

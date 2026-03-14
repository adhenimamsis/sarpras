<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\MonitoringMfk;
use App\Models\User;
use App\Models\UtilityLog;
use App\Observers\AssetObserver;
use App\Observers\MonitoringMfkObserver;
use App\Observers\UtilityLogObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * 1. DATABASE SECURITY & OBSERVERS
         * Mendaftarkan Observers sebagai audit trail (CCTV Database).
         * Penting untuk melacak siapa yang mengubah data inventaris & log utilitas.
         */
        if (Schema::hasTable('assets')) {
            Asset::observe(AssetObserver::class);
        }

        if (Schema::hasTable('monitoring_mfks')) {
            MonitoringMfk::observe(MonitoringMfkObserver::class);
        }

        if (Schema::hasTable('utility_logs')) {
            UtilityLog::observe(UtilityLogObserver::class);
        }

        /*
         * 1b. ROLE-BASED AUTHORIZATION (REPORT MODULES)
         * Memisahkan akses laporan operasional vs laporan sensitif.
         */
        Gate::define('reports.view.sensitive', function (User $user): bool {
            if (! $user->is_active) {
                return false;
            }

            return in_array($user->role, ['admin', 'kapus'], true);
        });

        Gate::define('reports.view.operational', function (User $user): bool {
            if (! $user->is_active) {
                return false;
            }

            return in_array($user->role, ['admin', 'staff', 'teknisi', 'kapus'], true);
        });

        Gate::define('reports.export.operational', function (User $user): bool {
            if (! $user->is_active) {
                return false;
            }

            return in_array($user->role, ['admin', 'staff', 'kapus'], true);
        });

        // Hardening: hindari unguard global untuk mencegah mass-assignment tak disengaja.
        Model::reguard();

        /*
         * 2. PERFORMANCE & INTEGRITY MONITORING (Strict Mode)
         * Mencegah N+1 Query yang bikin dashboard monitoring jadi lambat.
         * Memberi peringatan jika ada data yang "hilang" saat proses simpan.
         */
        if (! $this->app->isProduction()) {
            Model::preventLazyLoading();
            Model::preventSilentlyDiscardingAttributes();
            Model::preventAccessingMissingAttributes();
        }

        /*
         * 3. LOCALIZATION & TIMEZONE (INDONESIA)
         * Menjamin waktu laporan MFK, Kalibrasi, dan Log Utilitas sesuai WIB (Asia/Jakarta).
         * Format tanggal di PDF otomatis menggunakan bahasa Indonesia (id).
         */
        config(['app.locale' => 'id']);
        config(['app.timezone' => 'Asia/Jakarta']);
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID');
        date_default_timezone_set('Asia/Jakarta');

        /*
         * 4. SCHEMA & DATABASE COMPATIBILITY
         * Mencegah error "key too long" pada versi MySQL/MariaDB lama di Puskesmas.
         */
        Schema::defaultStringLength(191);

        /*
         * 5. HTTPS ENFORCEMENT
         * Memastikan aset (CSS/JS/Gambar QR/Logo) dipanggil aman via HTTPS di lingkungan produksi.
         */
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

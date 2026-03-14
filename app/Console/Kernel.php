<?php

namespace App\Console;

use App\Models\Asset;
use App\Models\LaporanKerusakan;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Tentukan jadwal otomatisasi (cron).
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function (): void {
            $batasTanggal = Carbon::today()->addDays(30);

            $alkesWarning = Asset::query()
                ->where('status_kalibrasi', true)
                ->whereNotNull('tgl_kalibrasi_selanjutnya')
                ->whereDate('tgl_kalibrasi_selanjutnya', '<=', $batasTanggal)
                ->get();

            $aparWarning = Asset::query()
                ->where('nama_alat', 'LIKE', '%APAR%')
                ->whereNotNull('tgl_kadaluarsa')
                ->whereDate('tgl_kadaluarsa', '<=', $batasTanggal)
                ->get();

            if ($alkesWarning->isEmpty() && $aparWarning->isEmpty()) {
                return;
            }

            $pesan = "[SIMSARPRAS] Reminder Mingguan\n\n";
            $pesan .= "Rekap aset yang butuh perhatian:\n\n";

            if ($alkesWarning->isNotEmpty()) {
                $pesan .= 'Kalibrasi jatuh tempo: '.$alkesWarning->count()." alat\n";
                foreach ($alkesWarning->take(3) as $item) {
                    $tanggal = $item->tgl_kalibrasi_selanjutnya?->format('d/m/Y') ?? '-';
                    $pesan .= "- {$item->nama_alat} ({$tanggal})\n";
                }
                if ($alkesWarning->count() > 3) {
                    $pesan .= "- dan lainnya\n";
                }
                $pesan .= "\n";
            }

            if ($aparWarning->isNotEmpty()) {
                $pesan .= 'APAR kadaluarsa: '.$aparWarning->count()." unit\n";
                foreach ($aparWarning->take(3) as $item) {
                    $tanggal = $item->tgl_kadaluarsa?->format('d/m/Y') ?? '-';
                    $pesan .= "- {$item->nama_alat} ({$tanggal})\n";
                }
                if ($aparWarning->count() > 3) {
                    $pesan .= "- dan lainnya\n";
                }
            }

            $pesan .= "\nDetail lengkap tersedia di dashboard SimSarpras.";

            LaporanKerusakan::sendWA(null, $pesan);
        })->weeklyOn(1, '08:00');
    }

    /**
     * Daftarkan commands untuk aplikasi.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

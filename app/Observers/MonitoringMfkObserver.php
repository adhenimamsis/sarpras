<?php

namespace App\Observers;

use App\Models\MonitoringMfk;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class MonitoringMfkObserver
{
    /**
     * Handle the MonitoringMfk "created" event.
     * Dipicu saat petugas baru saja selesai input checklist monitoring.
     */
    public function created(MonitoringMfk $monitoringMfk): void
    {
        $this->checkCriticalStatus($monitoringMfk, 'Input Baru');
    }

    /**
     * Handle the MonitoringMfk "updated" event.
     * Dipicu saat ada perubahan data monitoring (misal status berubah).
     */
    public function updated(MonitoringMfk $monitoringMfk): void
    {
        // Hanya kirim notifikasi jika statusnya memang berubah
        if ($monitoringMfk->isDirty('status')) {
            $this->checkCriticalStatus($monitoringMfk, 'Pembaruan Status');
        }
    }

    /**
     * Logika Utama: Cek apakah status bersifat kritis (MFK Alert).
     */
    private function checkCriticalStatus(MonitoringMfk $monitoring, string $actionType): void
    {
        $criticalStatuses = ['Rusak', 'Gangguan', 'Perbaikan', 'Temuan'];
        $status = ucfirst($monitoring->status);

        if (in_array($status, $criticalStatuses)) {
            // 1. Ambil semua User dengan role Admin atau Koordinator Sarpras
            // Asumsi: Admin perlu tahu setiap ada temuan kritis
            $recipients = User::all(); // Bos bisa filter berdasarkan role jika sudah ada Spatie Permissions

            // 2. Kirim Notifikasi ke Panel Filament (Lonceng)
            Notification::make()
                ->title('⚠️ TEMUAN MFK: '.$monitoring->jenis_utilitas)
                ->danger()
                ->icon('heroicon-o-exclamation-triangle')
                ->body("Ditemukan kondisi **{$status}** pada pemeriksaan {$monitoring->jenis_utilitas}. 
                        \nPetugas: ".($monitoring->user->name ?? 'Staf Sarpras'))
                ->actions([
                    Action::make('view')
                        ->label('Lihat Detail')
                        ->url('/admin/monitoring-mfks/'.$monitoring->id.'/edit')
                        ->button(),
                ])
                ->sendToDatabase($recipients);

            // 3. Catat di Log Sistem (untuk audit trail)
            Log::warning("MFK ALERT [{$actionType}]: {$monitoring->jenis_utilitas} berstatus {$status}. Dilaporkan oleh ID User: ".$monitoring->user_id);
        }
    }

    /**
     * Handle the MonitoringMfk "deleted" event.
     */
    public function deleted(MonitoringMfk $monitoringMfk): void
    {
        Log::info("Data Monitoring MFK ID: {$monitoringMfk->id} telah dihapus.");
    }
}

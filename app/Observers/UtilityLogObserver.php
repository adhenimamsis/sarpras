<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UtilityLog;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class UtilityLogObserver
{
    /**
     * Handle the UtilityLog "created" event.
     * Mengecek kondisi utilitas sesaat setelah data diinput.
     */
    public function created(UtilityLog $utilityLog): void
    {
        $this->analyzeUtilityData($utilityLog);
    }

    /**
     * Handle the UtilityLog "updated" event.
     */
    public function updated(UtilityLog $utilityLog): void
    {
        // Jalankan analisa jika ada perubahan pada angka kritis
        if ($utilityLog->isDirty(['angka_meteran', 'voltase', 'suhu'])) {
            $this->analyzeUtilityData($utilityLog);
        }
    }

    /**
     * Logika Analisa Data Utilitas (Suhu, Listrik, & BBM).
     */
    private function analyzeUtilityData(UtilityLog $log): void
    {
        $recipients = User::all(); // Idealnya filter user dengan role 'Admin' atau 'Sanitarian'
        $notifications = [];

        // 1. CEK SUHU SERVER (Standar MFK: Max 27-30°C)
        if ($log->lokasi === 'Server' && $log->suhu > 30) {
            $notifications[] = [
                'title' => '🔥 ALERT: SUHU SERVER OVERHEAT',
                'body' => "Suhu Ruang Server mencapai **{$log->suhu}°C**. Segera cek sistem pendingin AC!",
                'color' => 'danger',
                'icon' => 'heroicon-o-fire',
            ];
        }

        // 2. CEK TEGANGAN LISTRIK (Standar: 200V - 240V)
        if ($log->jenis === 'listrik' && ($log->voltase < 200 || $log->voltase > 240)) {
            $notifications[] = [
                'title' => '⚡ ALERT: TEGANGAN TIDAK STABIL',
                'body' => "Tegangan terdeteksi **{$log->voltase}V**. Risiko kerusakan alat medis elektronik!",
                'color' => 'warning',
                'icon' => 'heroicon-o-bolt-slash',
            ];
        }

        // 3. CEK BBM GENSET (Warning jika di bawah 30%)
        if ($log->jenis === 'solar' && $log->angka_meteran < 30) {
            $notifications[] = [
                'title' => '⛽ ALERT: STOK BBM KRITIS',
                'body' => "Sisa BBM Solar Genset hanya **{$log->angka_meteran}%**. Segera lakukan pengisian ulang!",
                'color' => 'danger',
                'icon' => 'heroicon-o-beaker',
            ];
        }

        // Eksekusi Pengiriman Notifikasi
        foreach ($notifications as $note) {
            Notification::make()
                ->title($note['title'])
                ->body($note['body'])
                ->color($note['color'])
                ->icon($note['icon'])
                ->actions([
                    Action::make('view')->label('Cek Dashboard')->url(route('monitoring.listrik'))->button(),
                ])
                ->persistent() // Notifikasi tidak hilang sampai dibaca
                ->sendToDatabase($recipients);

            // Catat di log sistem untuk audit akreditasi
            Log::channel('single')->warning($note['title'].': '.$note['body']);
        }
    }
}

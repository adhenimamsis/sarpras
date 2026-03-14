<?php

namespace App\Filament\Resources\MonitoringMfkResource\Pages;

use App\Filament\Resources\MonitoringMfkResource;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMonitoringMfk extends CreateRecord
{
    protected static string $resource = MonitoringMfkResource::class;

    /**
     * Judul halaman dibuat lebih spesifik untuk keperluan Akreditasi Puskesmas (Standar MFK).
     */
    public function getHeading(): string
    {
        return 'Input Checklist Monitoring MFK';
    }

    public function getSubheading(): ?string
    {
        return 'Dokumentasikan kondisi fasilitas dan utilitas secara akurat sesuai pemeriksaan lapangan.';
    }

    /**
     * Memproses data sebelum masuk ke database.
     * Memastikan standarisasi teks, kelengkapan tanggal, dan pencatatan petugas.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Standarisasi Teks (Uppercase) untuk Jenis Utilitas agar rapi di Laporan
        if (isset($data['jenis_utilitas'])) {
            $data['jenis_utilitas'] = strtoupper(trim($data['jenis_utilitas']));
        }

        // 2. Jika tanggal cek kosong, otomatis isi dengan hari ini
        if (empty($data['tgl_cek'])) {
            $data['tgl_cek'] = Carbon::now()->toDateString();
        }

        // 3. Otomatis catat ID Petugas yang sedang login
        $data['user_id'] = Auth::id();

        return $data;
    }

    /**
     * Redirect kembali ke daftar monitoring setelah berhasil simpan.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi dinamis yang memberikan feedback instan berdasarkan temuan.
     * Memberikan peringatan merah jika ditemukan gangguan pada fasilitas.
     */
    protected function getCreatedNotification(): ?Notification
    {
        /** @var \App\Models\MonitoringMfk $record */
        $record = $this->record;

        $jenis = $record->jenis_utilitas ?? 'Fasilitas';
        $status = $record->status ?? 'Normal';

        // Tentukan apakah temuan ini bersifat kritis
        $isDanger = in_array(ucfirst($status), ['Gangguan', 'Perbaikan', 'Rusak', 'Temuan']);

        if ($isDanger) {
            return Notification::make()
                ->title('PERHATIAN: Temuan MFK Dicatat!')
                ->body("Monitoring **{$jenis}** menunjukkan status **{$status}**. Segera koordinasikan dengan tim teknis.")
                ->danger()
                ->icon('heroicon-o-exclamation-triangle')
                ->persistent() // Notifikasi tidak hilang sampai diklik (agar petugas sadar ada masalah)
                ->send();
        }

        return Notification::make()
            ->title('Data MFK Berhasil Dicatat')
            ->body("Kondisi **{$jenis}** dalam status **{$status}** (Aman).")
            ->success()
            ->icon('heroicon-o-shield-check')
            ->send();
    }
}

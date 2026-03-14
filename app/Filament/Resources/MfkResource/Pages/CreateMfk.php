<?php

namespace App\Filament\Resources\MfkResource\Pages;

use App\Filament\Resources\MfkResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMfk extends CreateRecord
{
    protected static string $resource = MfkResource::class;

    /**
     * Mengatur judul halaman.
     */
    protected static ?string $title = 'Input Pemeliharaan Baru';

    /**
     * Redirect ke halaman daftar (Index) setelah berhasil simpan.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Kustomisasi Notifikasi Sukses
     * Memberitahu user bahwa status aset juga ikut berubah otomatis.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Data Pemeliharaan Tersimpan')
            ->body('Riwayat servis berhasil dicatat dan status kondisi aset telah diperbarui otomatis.')
            ->duration(5000); // Tampil selama 5 detik
    }

    /**
     * Mengubah label tombol Create (Opsional).
     */
    protected function getFormActions(): array
    {
        return parent::getFormActions();
    }
}

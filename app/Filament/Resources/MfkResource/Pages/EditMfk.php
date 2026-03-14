<?php

namespace App\Filament\Resources\MfkResource\Pages;

use App\Filament\Resources\MfkResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMfk extends EditRecord
{
    protected static string $resource = MfkResource::class;

    protected static ?string $title = 'Ubah Data Pemeliharaan';

    /**
     * Tombol aksi di bagian header (Kanan atas).
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus Log')
                ->modalHeading('Hapus Riwayat Pemeliharaan?')
                ->modalDescription('Tindakan ini tidak dapat dibatalkan. Data akan hilang permanen.'),
        ];
    }

    /**
     * Redirect kembali ke tabel utama setelah klik Save.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi sukses saat update data.
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Perubahan Disimpan')
            ->body('Data pemeliharaan MFK telah berhasil diperbarui.')
            ->icon('heroicon-o-check-circle')
            ->color('success');
    }

    /**
     * Judul di Breadcrumb.
     */
    public function getBreadcrumb(): string
    {
        return 'Ubah';
    }
}

<?php

namespace App\Filament\Resources\UtilityLogResource\Pages;

use App\Filament\Resources\UtilityLogResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUtilityLog extends CreateRecord
{
    protected static string $resource = UtilityLogResource::class;

    /**
     * Judul Halaman yang tampil di bagian atas.
     */
    public function getHeading(): string
    {
        return 'Tambah Catatan Utilitas / SARPRAS';
    }

    /**
     * Redirect setelah sukses (Default jika hanya klik Simpan).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Menambahkan tombol aksi khusus di form.
     */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(), // Tombol Simpan standar

            // Tombol Simpan & Tambah Lagi (Cocok untuk entry data massal)
            Actions\Action::make('saveAndCreateAnother')
                ->label('Simpan & Tambah Lagi')
                ->action('createAnother')
                ->color('info')
                ->button(),

            $this->getCancelFormAction(), // Tombol Batal
        ];
    }

    /**
     * Notifikasi custom saat data berhasil disimpan.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Catatan Disimpan!')
            ->body('Riwayat kondisi utilitas baru berhasil dicatat ke sistem.')
            ->icon('heroicon-o-clipboard-document-check');
    }
}

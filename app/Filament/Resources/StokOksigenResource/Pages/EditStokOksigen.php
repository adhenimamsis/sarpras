<?php

namespace App\Filament\Resources\StokOksigenResource\Pages;

use App\Filament\Resources\StokOksigenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStokOksigen extends EditRecord
{
    protected static string $resource = StokOksigenResource::class;

    /**
     * Judul halaman edit agar lebih spesifik.
     */
    protected static ?string $title = 'Ubah Data Stok Oksigen';

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Hapus dengan konfirmasi yang lebih jelas
            Actions\DeleteAction::make()
                ->label('Hapus Record')
                ->modalHeading('Hapus Data Stok?')
                ->modalDescription('Apakah Anda yakin ingin menghapus data mutasi stok ini? Tindakan ini tidak dapat dibatalkan.'),

            // Tombol Batal/Kembali di pojok kanan atas
            Actions\Action::make('back')
                ->label('Kembali ke Daftar')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    /**
     * REDIRECT: Setelah klik 'Save', otomatis kembali ke tabel utama.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * NOTIFIKASI: Pesan sukses update.
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data mutasi stok oksigen berhasil diperbarui!';
    }
}

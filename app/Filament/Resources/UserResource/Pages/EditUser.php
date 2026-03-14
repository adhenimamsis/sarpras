<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Judul halaman edit agar lebih jelas.
     */
    protected static ?string $title = 'Ubah Data Pengguna';

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Hapus dengan konfirmasi ekstra
            Actions\DeleteAction::make()
                ->label('Hapus Akun')
                ->modalHeading('Konfirmasi Hapus Pengguna')
                ->modalDescription('Apakah Anda yakin ingin menghapus akun ini? Pengguna tersebut tidak akan bisa login lagi ke sistem.'),

            // Tombol Navigasi Cepat untuk balik ke tabel
            Actions\Action::make('back')
                ->label('Kembali ke Daftar')
                ->color('gray')
                ->icon('heroicon-m-arrow-left')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    /**
     * REDIRECT: Setelah update, balik ke halaman daftar.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * NOTIFIKASI: Pesan sukses update.
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->icon('heroicon-o-check-badge')
            ->title('Data User Diperbarui')
            ->body('Perubahan data user telah berhasil disimpan ke dalam sistem.');
    }

    /**
     * HOOK: Jalankan logika sebelum data disimpan.
     * Berguna untuk membersihkan data seperti spasi atau format email.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Pastikan email tetap lowercase
        if (isset($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }

        return $data;
    }
}

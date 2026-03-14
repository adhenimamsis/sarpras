<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Judul halaman agar lebih jelas.
     */
    protected static ?string $title = 'Tambah Pengguna Baru';

    /**
     * REDIRECT: Setelah simpan, balik ke halaman daftar (Index).
     * Memudahkan Bos supaya tidak perlu klik back manual.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * NOTIFIKASI: Pesan sukses yang muncul di pojok kanan atas.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User Berhasil Dibuat')
            ->icon('heroicon-o-user-plus') // Tambah ikon biar lebih cakep
            ->body('Akun pegawai baru telah berhasil didaftarkan ke sistem.');
    }

    /**
     * HOOK: Jalankan sesuatu sebelum data disimpan.
     * Contoh: Memastikan password di-hash atau memberikan role default.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Contoh: Jika ingin memaksa email menjadi huruf kecil semua
        $data['email'] = strtolower($data['email']);

        return $data;
    }
}

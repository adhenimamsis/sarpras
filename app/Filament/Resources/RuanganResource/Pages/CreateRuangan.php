<?php

namespace App\Filament\Resources\RuanganResource\Pages;

use App\Filament\Resources\RuanganResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRuangan extends CreateRecord
{
    protected static string $resource = RuanganResource::class;

    /**
     * Mengatur judul halaman create secara dinamis.
     */
    protected static ?string $title = 'Tambah Data Ruangan';

    /**
     * REDIRECT: Setelah klik 'Create', otomatis kembali ke tabel utama.
     * Ini mencegah Bos harus klik tombol 'Back' manual.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * NOTIFIKASI: Pesan sukses custom setelah simpan data.
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data Ruangan berhasil disimpan ke sistem!';
    }
}

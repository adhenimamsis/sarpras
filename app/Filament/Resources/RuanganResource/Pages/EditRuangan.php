<?php

namespace App\Filament\Resources\RuanganResource\Pages;

use App\Filament\Resources\RuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRuangan extends EditRecord
{
    protected static string $resource = RuanganResource::class;

    /**
     * Judul halaman edit agar lebih spesifik.
     */
    protected static ?string $title = 'Ubah Data Ruangan';

    protected function getHeaderActions(): array
    {
        return [
            // Tombol hapus yang sudah ada
            Actions\DeleteAction::make()
                ->label('Hapus Ruangan')
                ->modalHeading('Hapus Data Ruangan?')
                ->modalDescription('Apakah Anda yakin? Semua data aset di dalam ruangan ini mungkin akan terpengaruh.'),

            // Tombol tambahan untuk navigasi balik dengan cepat
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
     * NOTIFIKASI: Pesan sukses custom setelah update data.
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Perubahan data ruangan berhasil disimpan!';
    }
}

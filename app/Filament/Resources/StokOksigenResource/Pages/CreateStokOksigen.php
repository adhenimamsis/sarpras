<?php

namespace App\Filament\Resources\StokOksigenResource\Pages;

use App\Filament\Resources\StokOksigenResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateStokOksigen extends CreateRecord
{
    protected static string $resource = StokOksigenResource::class;

    /**
     * Judul halaman yang lebih informatif.
     */
    protected static ?string $title = 'Input Stok Oksigen Baru';

    /**
     * REDIRECT: Balik ke tabel utama setelah simpan.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * NOTIFIKASI: Pesan sukses simpan data.
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data Stok Oksigen Berhasil Disimpan!';
    }

    /**
     * HOOK: Jalankan logika setelah data tersimpan.
     * Kita cek apakah stok yang diinput masuk kategori kritis.
     */
    protected function afterCreate(): void
    {
        $stok = $this->record;

        // Logika: Jika jumlah tabung kurang dari atau sama dengan 5
        if ($stok->jumlah_tabung <= 5) {
            Notification::make()
                ->warning()
                ->title('PERINGATAN: Stok Oksigen Kritis!')
                ->body("Stok di {$stok->lokasi} tersisa {$stok->jumlah_tabung} tabung. Segera lakukan pemesanan ulang!")
                ->persistent() // Notifikasi tidak hilang sampai diklik
                ->actions([
                    Action::make('cek_stok')
                        ->label('Lihat Detail')
                        ->button()
                        ->url(StokOksigenResource::getUrl('index')),
                ])
                ->sendToDatabase(auth()->user()); // Kirim juga ke database notification center
        }
    }
}

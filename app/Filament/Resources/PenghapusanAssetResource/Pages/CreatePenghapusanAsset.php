<?php

namespace App\Filament\Resources\PenghapusanAssetResource\Pages;

use App\Filament\Resources\PenghapusanAssetResource;
use App\Models\Asset;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePenghapusanAsset extends CreateRecord
{
    protected static string $resource = PenghapusanAssetResource::class;

    /**
     * Judul halaman yang lebih spesifik untuk keperluan inventaris barang daerah.
     */
    protected static ?string $title = 'Proses Penghapusan Aset (Non-Aktif)';

    /**
     * Memproses data sebelum disimpan ke database.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Menambahkan prefix otomatis pada nomor berita acara jika belum diisi
        if (empty($data['no_sk'])) {
            $data['no_sk'] = 'BA-PH/'.now()->format('Y/m').'/'.rand(100, 999);
        }

        return $data;
    }

    /**
     * AUTOMATIC ASSET UPDATE:
     * Begitu data penghapusan dibuat, status di tabel ASET otomatis berubah.
     */
    protected function afterCreate(): void
    {
        $record = $this->record;

        // Menggunakan Database Transaction untuk memastikan kedua data tersinkronisasi
        DB::transaction(function () use ($record) {
            $asset = Asset::find($record->asset_id);

            if ($asset) {
                $asset->update([
                    'status_ketersediaan' => 'Dihapuskan',
                    'is_active' => false, // Aset tidak lagi muncul di pencarian aktif/KIR
                    'keterangan' => trim(($asset->keterangan ?? '')." [Dihapuskan via BA: {$record->no_sk}]"),
                ]);
            }
        });
    }

    /**
     * REDIRECT: Setelah berhasil, kembali ke daftar penghapusan.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * NOTIFIKASI: Pesan sukses yang lebih informatif.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->icon('heroicon-o-trash')
            ->title('Penghapusan Berhasil')
            ->body('Aset telah dipindahkan ke daftar non-aktif dan berita acara telah dicatat.');
    }
}

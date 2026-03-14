<?php

namespace App\Filament\Resources\PemeliharaanResource\Pages;

use App\Filament\Resources\PemeliharaanResource;
use App\Models\Asset;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePemeliharaan extends CreateRecord
{
    protected static string $resource = PemeliharaanResource::class;

    /**
     * Judul halaman yang lebih profesional.
     */
    public function getHeading(): string
    {
        return 'Buat Jadwal Pemeliharaan (MFK)';
    }

    /**
     * Memproses data sebelum disimpan ke database.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Standarisasi Kode Pemeliharaan jika kosong (Contoh: MNT-2024-0001)
        if (empty($data['kode_pemeliharaan'])) {
            $data['kode_pemeliharaan'] = 'MNT-'.now()->format('Ymd').'-'.rand(100, 999);
        }

        return $data;
    }

    /**
     * Aksi setelah data berhasil dibuat.
     * Mengubah status aset terkait secara otomatis agar sinkron dengan Dashboard.
     */
    protected function afterCreate(): void
    {
        $pemeliharaan = $this->record;

        // DB Transaction untuk keamanan data
        DB::transaction(function () use ($pemeliharaan) {
            $asset = Asset::find($pemeliharaan->asset_id);

            if ($asset) {
                // Update status di tabel Asset agar Dashboard menampilkan kondisi "Sedang Diservis"
                $asset->update([
                    'status_ketersediaan' => 'Dalam Pemeliharaan',
                ]);
            }
        });
    }

    /**
     * Redirect kembali ke daftar (Index) setelah sukses.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi sukses dengan detail aset.
     */
    protected function getCreatedNotification(): ?Notification
    {
        $assetName = $this->record->asset->nama_alat ?? 'Aset';

        return Notification::make()
            ->success()
            ->icon('heroicon-o-wrench-screwdriver')
            ->title('Jadwal Pemeliharaan Diterbitkan')
            ->body("Agenda pemeliharaan untuk **{$assetName}** telah berhasil dijadwalkan dan status aset diperbarui.")
            ->send();
    }
}

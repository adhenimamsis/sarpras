<?php

namespace App\Filament\Resources\PemeliharaanResource\Pages;

use App\Filament\Resources\PemeliharaanResource;
use App\Models\Asset;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditPemeliharaan extends EditRecord
{
    protected static string $resource = PemeliharaanResource::class;

    /**
     * Tombol Aksi di Header (Pojok Kanan Atas).
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus Log')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Riwayat Pemeliharaan?')
                ->modalDescription('Data yang dihapus tidak dapat dikembalikan.'),
        ];
    }

    /**
     * Sinkronisasi Otomatis ke Tabel Asset setelah data diupdate.
     */
    protected function afterSave(): void
    {
        $record = $this->record;

        // DB Transaction untuk menjaga integritas data
        DB::transaction(function () use ($record) {
            $asset = Asset::find($record->asset_id);

            if ($asset) {
                // LOGIKA 1: Jika status pemeliharaan SELESAI
                if ($record->status === 'Selesai') {
                    $asset->update([
                        'status_ketersediaan' => 'Tersedia', // Kembalikan ke siap pakai
                        'tgl_maintenance_terakhir' => $record->tgl_pemeliharaan,
                        // Otomatis jadwalkan 6 bulan ke depan untuk standar MFK
                        'tgl_maintenance_berikutnya' => Carbon::parse($record->tgl_pemeliharaan)->addMonths(6),
                    ]);
                }
                // LOGIKA 2: Jika masih dalam proses atau tertunda
                elseif (in_array($record->status, ['Proses', 'Pending'])) {
                    $asset->update([
                        'status_ketersediaan' => 'Dalam Pemeliharaan',
                    ]);
                }
            }
        });
    }

    /**
     * Redirect kembali ke halaman list agar admin bisa langsung melihat perubahan.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi sukses yang lebih interaktif.
     */
    protected function getSavedNotification(): ?Notification
    {
        $status = $this->record->status;
        $assetName = $this->record->asset->nama_alat ?? 'Aset';

        return Notification::make()
            ->success()
            ->icon($status === 'Selesai' ? 'heroicon-o-check-circle' : 'heroicon-o-arrow-path')
            ->title('Update Berhasil')
            ->body("Status pemeliharaan **{$assetName}** kini adalah **{$status}**. Data aset terkait telah disinkronkan.")
            ->duration(5000);
    }
}

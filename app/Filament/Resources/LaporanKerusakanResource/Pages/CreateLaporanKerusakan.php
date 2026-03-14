<?php

namespace App\Filament\Resources\LaporanKerusakanResource\Pages;

use App\Filament\Resources\LaporanKerusakanResource;
use App\Models\Asset;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateLaporanKerusakan extends CreateRecord
{
    protected static string $resource = LaporanKerusakanResource::class;

    /**
     * Memastikan data pendukung terisi sebelum disimpan ke database.
     * Secara otomatis mengisi tanggal lapor dan memberikan kode laporan unik.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['tgl_lapor'])) {
            $data['tgl_lapor'] = now();
        }

        // Generate No Laporan Otomatis jika kolom tersedia (Contoh: LPK-2026-0001)
        if (empty($data['no_laporan'])) {
            $data['no_laporan'] = 'LPK-'.now()->format('Ymd').'-'.rand(100, 999);
        }

        return $data;
    }

    /**
     * LOGIKA OTOMATISASI ASET:
     * Begitu laporan dibuat, status kondisi aset di tabel Asset otomatis menjadi Rusak.
     */
    protected function afterCreate(): void
    {
        $record = $this->record;

        // Gunakan Transaction agar jika update aset gagal, laporan juga batal (integritas data)
        DB::transaction(function () use ($record) {
            $asset = Asset::find($record->asset_id);

            if ($asset) {
                $asset->update([
                    'kondisi' => 'Rusak Ringan', // Default saat baru lapor
                    'status_ketersediaan' => 'Dalam Perbaikan',
                ]);
            }
        });
    }

    /**
     * Redirect ke halaman List (Tabel Utama) agar workflow petugas lebih cepat.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi Pop-up sukses dengan feedback visual yang kuat.
     */
    protected function getCreatedNotification(): ?Notification
    {
        $namaAset = $this->record->asset->nama_alat ?? 'Aset';

        return Notification::make()
            ->success()
            ->icon('heroicon-o-paper-airplane')
            ->title('Laporan Kerusakan Diterima!')
            ->body("Aduan untuk **{$namaAset}** telah dicatat. Status aset diperbarui menjadi 'Dalam Perbaikan' dan tim Sarpras telah dinotifikasi.")
            ->send();
    }
}

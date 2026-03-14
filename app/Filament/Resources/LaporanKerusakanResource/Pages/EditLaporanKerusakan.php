<?php

namespace App\Filament\Resources\LaporanKerusakanResource\Pages;

use App\Filament\Resources\LaporanKerusakanResource;
use App\Models\Asset;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditLaporanKerusakan extends EditRecord
{
    protected static string $resource = LaporanKerusakanResource::class;

    /**
     * Judul halaman edit yang dinamis berdasarkan nama aset.
     */
    public function getHeading(): string
    {
        return 'Tindak Lanjut Perbaikan: '.($this->record->asset->nama_alat ?? 'Aset');
    }

    /**
     * Menampilkan status laporan visual di bawah judul.
     */
    public function getSubheading(): ?string
    {
        $status = $this->record->status;
        $label = match ($status) {
            'Lapor' => '🔴 Menunggu Respon',
            'Proses' => '🟡 Dalam Perbaikan',
            'Selesai' => '🟢 Perbaikan Selesai',
            default => '⚪ Status: '.$status,
        };

        return $label.' | ID Laporan: '.($this->record->no_laporan ?? $this->record->id);
    }

    /**
     * LOGIKA SINKRONISASI ASET:
     * Mengotomatisasi kondisi aset di tabel utama berdasarkan hasil perbaikan.
     */
    protected function afterSave(): void
    {
        $record = $this->record;

        DB::transaction(function () use ($record) {
            $asset = Asset::find($record->asset_id);

            if ($asset) {
                // Jika status perbaikan diubah menjadi SELESAI
                if ($record->status === 'Selesai') {
                    $asset->update([
                        'kondisi' => 'Baik',
                        'status_ketersediaan' => 'Tersedia',
                    ]);
                }
                // Jika status perbaikan sedang PROSES
                elseif ($record->status === 'Proses') {
                    $asset->update([
                        'status_ketersediaan' => 'Dalam Perbaikan',
                    ]);
                }
            }
        });
    }

    /**
     * Tombol aksi di header.
     */
    protected function getHeaderActions(): array
    {
        return [
            // Tombol Cetak SPK / Bukti Laporan (Jika rute tersedia)
            Actions\Action::make('print_spk')
                ->label('Cetak SPK')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('laporan.kerusakan.print', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => \Illuminate\Support\Facades\Route::has('laporan.kerusakan.print')
                    && (auth()->user()?->can('reports.view.operational') ?? false)),

            Actions\DeleteAction::make()
                ->label('Hapus Laporan')
                ->icon('heroicon-o-trash'),
        ];
    }

    /**
     * Redirect kembali ke tabel utama agar alur kerja efisien.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi sukses dengan logika ikon yang menyesuaikan status.
     */
    protected function getSavedNotification(): ?Notification
    {
        $status = $this->record->status;

        return Notification::make()
            ->success()
            ->title('Update Berhasil')
            ->icon($status === 'Selesai' ? 'heroicon-o-check-badge' : 'heroicon-o-arrow-path')
            ->body("Status perbaikan aset **{$this->record->asset->nama_alat}** telah diperbarui menjadi **{$status}**.")
            ->send();
    }
}

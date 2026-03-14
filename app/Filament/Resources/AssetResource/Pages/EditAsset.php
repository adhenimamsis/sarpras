<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    /**
     * Tombol aksi di pojok kanan atas halaman edit.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Lihat Detail')
                ->icon('heroicon-o-eye')
                ->color('info'),

            Actions\DeleteAction::make()
                ->label('Hapus Aset')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Data KIB?')
                ->modalDescription('Apakah yakin ingin menghapus aset ini secara permanen?'),
        ];
    }

    /**
     * Memproses data otomatis sebelum disimpan (Update Data Standard KIB & MFK).
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 1. STANDARISASI HURUF (UPPERCASE)
        // Memastikan data tetap rapi sesuai format pelaporan Barang Milik Daerah (BMD)
        $fieldsToUppercase = ['nama_alat', 'no_sertifikat', 'alamat', 'merk', 'tipe', 'konstruksi', 'no_polisi'];
        foreach ($fieldsToUppercase as $field) {
            if (! empty($data[$field]) && is_string($data[$field])) {
                $data[$field] = strtoupper(trim($data[$field]));
            }
        }

        // 2. SINKRONISASI KODE ASPAK
        if (isset($data['kode_alat']) && empty($data['kode_aspak'])) {
            $data['kode_aspak'] = $data['kode_alat'];
        }

        // 3. LOGIKA KATEGORI KIB (Reset kolom yang tidak relevan)
        if (isset($data['kategori_kib']) && in_array($data['kategori_kib'], ['KIB_A', 'KIB_C', 'KIB_D'])) {
            $data['status_kalibrasi'] = false; // Non-alkes tidak butuh kalibrasi
            $data['tgl_kalibrasi_terakhir'] = null;
            $data['tgl_kalibrasi_selanjutnya'] = null;
        }

        // 4. OTOMATISASI JADWAL MAINTENANCE (MFK - 6 Bulanan)
        if (! empty($data['tgl_maintenance_terakhir'])) {
            $data['tgl_maintenance_berikutnya'] = Carbon::parse($data['tgl_maintenance_terakhir'])
                ->addMonths(6)
                ->toDateString();
        }

        // 5. OTOMATISASI JADWAL KALIBRASI (MFK - Tahunan)
        // Jika Alat Medis (KIB B) diupdate tanggal kalibrasinya, hitung ulang jatuh temponya
        if (isset($data['kategori_kib']) && $data['kategori_kib'] === 'KIB_B' && ! empty($data['tgl_kalibrasi_terakhir'])) {
            $data['tgl_kalibrasi_selanjutnya'] = Carbon::parse($data['tgl_kalibrasi_terakhir'])
                ->addYear()
                ->toDateString();
        }

        // 6. PROTEKSI KOLOM VIRTUAL / COMPUTED
        // Mencegah error SQL "Column not found" jika ada field hanya-baca di form
        unset($data['nilai_buku_display'], $data['umur_ekonomis_sisa']);

        return $data;
    }

    /**
     * Redirect kembali ke halaman daftar (Index) setelah sukses.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi sukses yang dinamis.
     */
    protected function getSavedNotification(): ?Notification
    {
        $record = $this->getRecord();

        return Notification::make()
            ->success()
            ->title('Perubahan Disimpan')
            ->body("Data aset **{$record->nama_alat}** telah berhasil diperbarui di sistem SimSarpras.")
            ->icon('heroicon-o-check-circle')
            ->duration(5000);
    }
}

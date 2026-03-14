<?php

namespace App\Filament\Resources\UtilityLogResource\Pages;

use App\Filament\Resources\UtilityLogResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUtilityLog extends EditRecord
{
    protected static string $resource = UtilityLogResource::class;

    /**
     * Judul Halaman yang dinamis berdasarkan jenis utilitas.
     */
    public function getHeading(): string
    {
        return 'Ubah Catatan '.ucfirst($this->record->jenis);
    }

    /**
     * Menampilkan info petugas dan waktu update terakhir.
     */
    public function getSubheading(): ?string
    {
        $petugas = $this->record->petugas ?? 'Sistem';
        $waktu = $this->record->updated_at ? $this->record->updated_at->format('d/m/Y H:i') : '-';

        return "Petugas terakhir: {$petugas} | Waktu: {$waktu}";
    }

    /**
     * Tombol aksi di header (Pojok kanan atas).
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus Catatan')
                ->modalHeading('Hapus Riwayat Log?')
                ->modalDescription('Tindakan ini tidak dapat dibatalkan.'),
        ];
    }

    /**
     * Kembali ke daftar log setelah berhasil simpan.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi sukses saat update data.
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Catatan Diperbarui')
            ->body('Perubahan log utilitas telah berhasil disimpan.')
            ->icon('heroicon-o-pencil-square');
    }
}

<?php

namespace App\Filament\Resources\MonitoringMfkResource\Pages;

use App\Filament\Resources\MonitoringMfkResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMonitoringMfk extends EditRecord
{
    protected static string $resource = MonitoringMfkResource::class;

    /**
     * Judul halaman edit yang dinamis berdasarkan jenis utilitas.
     */
    public function getHeading(): string
    {
        return 'Edit Monitoring: '.($this->record->jenis_utilitas ?? 'MFK');
    }

    /**
     * Menampilkan info tanggal pemeriksaan di bawah judul dengan proteksi null.
     */
    public function getSubheading(): ?string
    {
        $tanggal = $this->record->tgl_cek
            ? Carbon::parse($this->record->tgl_cek)->translatedFormat('d F Y')
            : '-';

        return "Pemeriksaan dilakukan pada {$tanggal}. Pastikan perubahan data sesuai dengan fakta lapangan.";
    }

    /**
     * Aksi di header: Hapus dan Cetak Langsung.
     */
    protected function getHeaderActions(): array
    {
        return [
            // Tombol Cetak Form Satuan (Sesuai rute di MonitoringMfkController)
            Actions\Action::make('print')
                ->label('Cetak Form')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('monitoring.mfk.print', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->can('reports.view.operational') ?? false),

            Actions\DeleteAction::make()
                ->label('Hapus Log')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Data Monitoring?')
                ->modalDescription('Apakah yakin ingin menghapus riwayat pemeriksaan ini? Data yang dihapus akan hilang dari laporan rekap bulanan.'),
        ];
    }

    /**
     * Standarisasi data sebelum disimpan ke database.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 1. Memastikan jenis utilitas tetap UPPERCASE agar sinkron dengan filter rekap
        if (isset($data['jenis_utilitas'])) {
            $data['jenis_utilitas'] = strtoupper(trim($data['jenis_utilitas']));
        }

        return $data;
    }

    /**
     * Kembali ke tabel utama setelah simpan agar workflow petugas lebih cepat.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi sukses dengan logika Alert jika status berubah menjadi kritis.
     */
    protected function getSavedNotification(): ?Notification
    {
        /** @var \App\Models\MonitoringMfk $record */
        $record = $this->record;

        $status = ucfirst($record->status ?? 'Aman');
        $isCritical = in_array($status, ['Gangguan', 'Perbaikan', 'Rusak', 'Temuan']);

        if ($isCritical) {
            return Notification::make()
                ->danger()
                ->icon('heroicon-o-exclamation-circle')
                ->title('Log Diperbarui: STATUS KRITIS')
                ->body("Monitoring **{$record->jenis_utilitas}** diperbarui dengan status **{$status}**. Harap segera tindak lanjuti!")
                ->persistent()
                ->send();
        }

        return Notification::make()
            ->success()
            ->icon('heroicon-o-check-badge')
            ->title('Perubahan Disimpan')
            ->body('Log monitoring harian berhasil diperbarui dan disinkronkan ke dalam sistem SimSarpras.')
            ->send();
    }
}

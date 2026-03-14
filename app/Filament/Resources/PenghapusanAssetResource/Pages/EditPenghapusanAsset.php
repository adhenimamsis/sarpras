<?php

namespace App\Filament\Resources\PenghapusanAssetResource\Pages;

use App\Filament\Resources\PenghapusanAssetResource;
use App\Models\Asset;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

// PASTIKAN NAMA CLASS: EditPenghapusanAsset (Pakai 'n', bukan 'm')
class EditPenghapusanAsset extends EditRecord
{
    protected static string $resource = PenghapusanAssetResource::class;

    /**
     * Judul halaman edit yang formal.
     */
    protected static ?string $title = 'Koreksi Data Penghapusan Aset';

    /**
     * Tombol Aksi di Header (Cetak & Hapus).
     */
    protected function getHeaderActions(): array
    {
        return [
            // TOMBOL CETAK: Langsung cetak Berita Acara (BA)
            Actions\Action::make('print_ba')
                ->label('Cetak BA')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('penghapusan.print', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->can('reports.view.sensitive') ?? false),

            // TOMBOL HAPUS LOG
            Actions\DeleteAction::make()
                ->label('Hapus Log')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Catatan Penghapusan?')
                ->modalDescription('Peringatan: Menghapus log ini tidak otomatis mengaktifkan kembali aset asal. Anda harus melakukannya manual di modul Aset.'),

            // TOMBOL KEMBALI
            Actions\Action::make('back')
                ->label('Batal')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    /**
     * AFTER SAVE HOOK:
     * Menjaga konsistensi data jika aset yang dipilih dalam penghapusan diubah.
     */
    protected function afterSave(): void
    {
        $record = $this->record;

        DB::transaction(function () use ($record) {
            $asset = Asset::find($record->asset_id);

            if ($asset) {
                $asset->update([
                    'status_ketersediaan' => 'Dihapuskan',
                    'is_active' => false,
                    'keterangan' => "Penghapusan diperbarui via BA: {$record->no_berita_acara}",
                ]);
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->icon('heroicon-o-check-badge')
            ->title('Data Diperbarui')
            ->body('Log penghapusan dan status aset terkait telah berhasil disinkronkan.');
    }
}

<?php

namespace App\Filament\Resources\PenghapusanAssetResource\Pages;

use App\Filament\Resources\PenghapusanAssetResource;
use App\Models\PenghapusanAsset;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPenghapusanAssets extends ListRecords
{
    protected static string $resource = PenghapusanAssetResource::class;

    /**
     * Judul halaman daftar yang lebih formal untuk keperluan administrasi.
     */
    protected static ?string $title = 'Log Penghapusan & Non-Aktif Aset';

    /**
     * Aksi Header: Cetak Laporan Massal & Input Data.
     */
    protected function getHeaderActions(): array
    {
        return [
            // 1. Tombol Cetak Rekap (Sesuai rute penamaan di web.php)
            Action::make('printRekap')
                ->label('Cetak Rekap (PDF)')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->tooltip('Download rekapitulasi aset yang telah dihapuskan')
                ->url(fn () => route('penghapusan.rekap'))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->can('reports.view.sensitive') ?? false),

            // 2. Tombol Tambah Data
            Actions\CreateAction::make()
                ->label('Proses Penghapusan')
                ->icon('heroicon-o-minus-circle')
                ->color('danger'),
        ];
    }

    /**
     * TAB FILTERING:
     * Mengelompokkan riwayat penghapusan berdasarkan alasan umum.
     */
    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Riwayat')
                ->icon('heroicon-m-clock')
                ->badge(PenghapusanAsset::count()),

            'rusak_berat' => Tab::make('Rusak Berat')
                ->icon('heroicon-m-trash')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('alasan', 'like', '%Rusak%'))
                ->badge(PenghapusanAsset::where('alasan', 'like', '%Rusak%')->count())
                ->badgeColor('danger'),

            'hilang' => Tab::make('Hilang / Lainnya')
                ->icon('heroicon-m-question-mark-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('alasan', 'not like', '%Rusak%'))
                ->badge(PenghapusanAsset::where('alasan', 'not like', '%Rusak%')->count())
                ->badgeColor('warning'),
        ];
    }

    /**
     * Default tab saat halaman dibuka.
     */
    public function getDefaultActiveTab(): string|int|null
    {
        return 'semua';
    }
}

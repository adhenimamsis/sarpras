<?php

namespace App\Filament\Resources\StokOksigenResource\Pages;

use App\Filament\Resources\StokOksigenResource;
use App\Models\StokOksigen;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStokOksigens extends ListRecords
{
    protected static string $resource = StokOksigenResource::class;

    /**
     * Judul Halaman.
     */
    protected static ?string $title = 'Monitoring Stok Oksigen';

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Export (Opsional jika ingin laporan bulanan)
            Actions\Action::make('cetak_laporan')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('cetak.stok-oksigen')) // Sesuaikan route cetak Anda
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->can('reports.view.operational') ?? false),

            Actions\CreateAction::make()
                ->label('Input Mutasi Stok')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    /**
     * Tabs untuk filter cepat berdasarkan ukuran tabung
     * Ditambahkan badge jumlah data agar lebih informatif.
     */
    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Riwayat')
                ->icon('heroicon-m-list-bullet'),

            'besar' => Tab::make('Tabung Besar (6m3)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('ukuran', '6m3'))
                ->badge(StokOksigen::where('ukuran', '6m3')->count())
                ->badgeColor('primary')
                ->icon('heroicon-m-circle-stack'),

            'kecil' => Tab::make('Tabung Kecil (1m3)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('ukuran', '1m3'))
                ->badge(StokOksigen::where('ukuran', '1m3')->count())
                ->badgeColor('success')
                ->icon('heroicon-m-stop'),
        ];
    }

    /**
     * Menampilkan statistik stok di bagian atas tabel
     * Catatan: Bos harus membuat file Widget-nya dulu jika ingin ini aktif.
     */
    protected function getHeaderWidgets(): array
    {
        return [
            // StokOksigenResource\Widgets\StokOksigenOverview::class,
        ];
    }
}

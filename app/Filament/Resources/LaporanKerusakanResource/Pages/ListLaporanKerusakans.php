<?php

namespace App\Filament\Resources\LaporanKerusakanResource\Pages;

use App\Filament\Resources\LaporanKerusakanResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLaporanKerusakans extends ListRecords
{
    protected static string $resource = LaporanKerusakanResource::class;

    /**
     * Header Actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            // TOMBOL CETAK REKAP (SINKRON DENGAN WEB.PHP)
            Action::make('cetak_rekap')
                ->label('Cetak Rekap')
                ->icon('heroicon-o-printer')
                ->color('info') // Mengubah ke biru info agar lebih elegan
                // FIX: Menggunakan nama rute yang sudah kita sinkronkan sebelumnya
                ->url(fn () => route('cetak.rekap.kerusakan', [
                    'status' => $this->activeTab ?? 'semua',
                ]))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->can('reports.view.operational') ?? false),

            // TOMBOL TAMBAH DATA
            Actions\CreateAction::make()
                ->label('Input Laporan Baru')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    /**
     * Menambahkan Tab Filter di atas tabel.
     * Monitoring real-time untuk melihat beban kerja tim teknis.
     */
    public function getTabs(): array
    {
        /** @var \App\Models\LaporanKerusakan $model */
        $model = $this->getModel();

        return [
            'semua' => Tab::make('Semua Laporan')
                ->icon('heroicon-m-list-bullet')
                ->badge(fn () => $model::count()),

            'baru' => Tab::make('Baru')
                ->label('🔴 Laporan Baru')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Lapor'))
                ->badge(fn () => $model::where('status', 'Lapor')->count())
                ->badgeColor('danger'),

            'proses' => Tab::make('Proses')
                ->label('🟡 Sedang Dikerjakan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Proses'))
                ->badge(fn () => $model::where('status', 'Proses')->count())
                ->badgeColor('warning'),

            'selesai' => Tab::make('Selesai')
                ->label('🟢 Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Selesai'))
                ->badge(fn () => $model::where('status', 'Selesai')->count())
                ->badgeColor('success'),
        ];
    }

    /**
     * Default tab saat halaman diakses.
     */
    public function getDefaultActiveTab(): string|int|null
    {
        return 'baru'; // Diubah ke 'baru' agar Admin langsung fokus pada laporan yang belum ditangani
    }
}

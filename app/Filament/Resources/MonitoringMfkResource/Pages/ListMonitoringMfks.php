<?php

namespace App\Filament\Resources\MonitoringMfkResource\Pages;

use App\Filament\Resources\MonitoringMfkResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMonitoringMfks extends ListRecords
{
    protected static string $resource = MonitoringMfkResource::class;

    /**
     * Tombol Aksi di Header.
     * Ditambahkan tombol Cetak Form Lapangan untuk mempermudah checklist manual.
     */
    protected function getHeaderActions(): array
    {
        return [
            // TOMBOL CETAK FORM KOSONG (UNTUK CHECKLIST MANUAL LAPANGAN)
            Action::make('cetak_form_lapangan')
                ->label('Cetak Form Lapangan')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->tooltip('Cetak form checklist kosong untuk dibawa saat inspeksi lapangan')
                ->url(fn () => route('monitoring.form.kosong'))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->can('reports.view.operational') ?? false),

            // TOMBOL CETAK REKAP MFK (Dinamis berdasarkan Tab)
            Action::make('cetak_rekap')
                ->label('Rekap Bulanan')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->tooltip('Cetak rekapitulasi sesuai kategori tab yang dipilih')
                ->url(fn (): string => route('monitoring.mfk.rekap', [
                    'status' => $this->activeTab ?? 'semua',
                ]))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->can('reports.view.operational') ?? false),

            // TOMBOL TAMBAH DATA
            Actions\CreateAction::make()
                ->label('Input Monitoring Baru')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),
        ];
    }

    /**
     * Pengelompokan Tab Monitoring Berdasarkan Status MFK (Standar Akreditasi).
     */
    public function getTabs(): array
    {
        /** @var \App\Models\MonitoringMfk $model */
        $model = $this->getModel();

        return [
            'semua' => Tab::make('Semua Riwayat')
                ->icon('heroicon-m-list-bullet')
                ->badge(fn () => $model::count())
                ->badgeColor('gray'),

            'normal' => Tab::make('Kondisi Normal')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Normal'))
                ->icon('heroicon-m-check-circle')
                ->badge(fn () => $model::where('status', 'Normal')->count())
                ->badgeColor('success'),

            'gangguan' => Tab::make('Gangguan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Gangguan'))
                ->icon('heroicon-m-x-circle')
                ->badge(fn () => $model::where('status', 'Gangguan')->count())
                ->badgeColor('danger'),

            'perbaikan' => Tab::make('Dalam Perbaikan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Perbaikan'))
                ->icon('heroicon-m-wrench-screwdriver')
                ->badge(fn () => $model::where('status', 'Perbaikan')->count())
                ->badgeColor('warning'),
        ];
    }

    /**
     * Menentukan tab default saat halaman dibuka.
     */
    public function getDefaultActiveTab(): string|int|null
    {
        return 'semua';
    }

    /**
     * Memberikan judul sub-halaman agar lebih informatif bagi Surveyor Akreditasi.
     */
    public function getSubheading(): ?string
    {
        return 'Data pemantauan fasilitas, utilitas, dan keselamatan lingkungan kerja Puskesmas Bendan.';
    }
}

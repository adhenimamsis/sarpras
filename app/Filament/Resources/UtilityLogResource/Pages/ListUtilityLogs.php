<?php

namespace App\Filament\Resources\UtilityLogResource\Pages;

use App\Filament\Resources\UtilityLogResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUtilityLogs extends ListRecords
{
    protected static string $resource = UtilityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Catatan Baru')
                ->icon('heroicon-m-plus'),
        ];
    }

    /**
     * Menambahkan Tabs di atas tabel untuk filter cepat berdasarkan jenis utilitas.
     */
    public function getTabs(): array
    {
        $model = $this->getModel();

        return [
            'all' => Tab::make('Semua Data')
                ->icon('heroicon-m-list-bullet'),

            'listrik' => Tab::make('Listrik')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis', 'listrik'))
                ->icon('heroicon-m-bolt')
                ->badge(fn () => $model::where('jenis', 'listrik')->count())
                ->badgeColor('warning'),

            'air' => Tab::make('Air Bersih')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis', 'air'))
                ->icon('heroicon-m-beaker')
                ->badge(fn () => $model::where('jenis', 'air')->count())
                ->badgeColor('info'),

            'ipal' => Tab::make('IPAL')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis', 'ipal'))
                ->icon('heroicon-m-arrow-path')
                ->badge(fn () => $model::where('jenis', 'ipal')->count())
                ->badgeColor('success'),

            'apar' => Tab::make('APAR')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis', 'apar'))
                ->icon('heroicon-m-fire')
                ->badge(fn () => $model::where('jenis', 'apar')->count())
                ->badgeColor('danger'),

            'bangunan' => Tab::make('Fasilitas/Gedung')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis', 'bangunan'))
                ->icon('heroicon-m-building-office')
                ->badge(fn () => $model::where('jenis', 'bangunan')->count())
                ->badgeColor('gray'),
        ];
    }

    /**
     * Mengatur default tab yang aktif saat halaman pertama kali dibuka.
     */
    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}

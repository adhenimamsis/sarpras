<?php

namespace App\Filament\Resources\KalibrasiResource\Pages;

use App\Filament\Resources\KalibrasiResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListKalibrasis extends ListRecords
{
    protected static string $resource = KalibrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Hasil Kalibrasi')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Data')
                ->icon('heroicon-m-rectangle-stack'),

            'layak' => Tab::make('Layak Pakai')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('hasil', 'Layak')
                        ->where('tgl_kadaluarsa', '>', now())
                )
                ->icon('heroicon-m-check-badge')
                ->badge(fn () => $this->getModel()::where('hasil', 'Layak')->where('tgl_kadaluarsa', '>', now())->count())
                ->badgeColor('success'),

            'tidak_layak' => Tab::make('Tidak Layak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('hasil', 'Tidak Layak'))
                ->icon('heroicon-m-x-circle')
                ->badge(fn () => $this->getModel()::where('hasil', 'Tidak Layak')->count())
                ->badgeColor('danger'),

            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tgl_kadaluarsa', '<', now()))
                ->icon('heroicon-m-clock')
                ->badge(fn () => $this->getModel()::where('tgl_kadaluarsa', '<', now())->count())
                ->badgeColor('warning'),
        ];
    }
}

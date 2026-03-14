<?php

namespace App\Filament\Resources\MfkResource\Pages;

use App\Filament\Resources\MfkResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords; // Pastikan ini ada
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class ListMfks extends ListRecords
{
    protected static string $resource = MfkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Pemeliharaan')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Data')
                ->icon('heroicon-m-list-bullet'),

            'perlu_servis' => Tab::make('Perlu Servis')
                // ->description(...) DIHAPUS karena tidak ada di class Tab
                ->icon('heroicon-m-exclamation-triangle')
                ->modifyQueryUsing(fn (Builder $query) => Schema::hasColumn('mfk_pemeliharaans', 'tgl_berikutnya')
                    ? $query->whereDate('tgl_berikutnya', '<=', now())
                    : $query->whereRaw('1 = 0'))
                ->badge(fn () => Schema::hasColumn('mfk_pemeliharaans', 'tgl_berikutnya')
                    ? $this->getResource()::getModel()::whereDate('tgl_berikutnya', '<=', now())->count()
                    : 0)
                ->badgeColor('danger'),

            'bulan_ini' => Tab::make('Bulan Ini')
                ->icon('heroicon-m-calendar-days')
                ->modifyQueryUsing(fn (Builder $query) => Schema::hasColumn('mfk_pemeliharaans', 'tgl_pemeliharaan')
                    ? $query->whereMonth('tgl_pemeliharaan', now()->month)
                        ->whereYear('tgl_pemeliharaan', now()->year)
                    : $query->whereRaw('1 = 0')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'semua';
    }
}

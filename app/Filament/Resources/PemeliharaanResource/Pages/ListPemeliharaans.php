<?php

namespace App\Filament\Resources\PemeliharaanResource\Pages;

use App\Filament\Resources\PemeliharaanResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPemeliharaans extends ListRecords
{
    protected static string $resource = PemeliharaanResource::class;

    /**
     * Tombol Aksi di Header.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Jadwal')
                ->icon('heroicon-o-calendar-days')
                ->color('primary'),
        ];
    }

    /**
     * TAB FILTERING:
     * Pengelompokan jadwal untuk memudahkan monitoring teknisi Sarpras.
     */
    public function getTabs(): array
    {
        // Menggunakan getModel() agar lebih fleksibel jika ada perubahan model di resource
        $model = $this->getModel();

        return [
            'semua' => Tab::make('Semua Jadwal')
                ->icon('heroicon-m-list-bullet')
                ->badge($model::count()),

            'mendatang' => Tab::make('Terjadwal')
                ->label('📅 Akan Datang')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Terjadwal'))
                ->badge($model::where('status', 'Terjadwal')->count())
                ->badgeColor('info'),

            'proses' => Tab::make('Proses')
                ->label('🛠️ Sedang Dikerjakan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Proses'))
                ->badge($model::where('status', 'Proses')->count())
                ->badgeColor('warning'),

            'selesai' => Tab::make('Selesai')
                ->label('✅ Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Selesai'))
                ->badge($model::where('status', 'Selesai')->count())
                ->badgeColor('success'),

            'batal' => Tab::make('Batal')
                ->label('❌ Dibatalkan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Batal'))
                ->badge($model::where('status', 'Batal')->count())
                ->badgeColor('danger'),
        ];
    }

    /**
     * Mengatur tab default saat halaman dibuka.
     */
    public function getDefaultActiveTab(): string|int|null
    {
        return 'semua';
    }
}

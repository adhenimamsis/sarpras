<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords; // Pastikan namespace Tab ini benar untuk v3
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    /**
     * Judul Halaman.
     */
    protected static ?string $title = 'Manajemen Pengguna';

    /**
     * Tombol aksi di header tabel.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah User Baru')
                ->icon('heroicon-m-user-plus'),
        ];
    }

    /**
     * Mengatur tab mana yang aktif saat pertama kali halaman dimuat.
     */
    public function getDefaultActiveTab(): string|int|null
    {
        return 'semua';
    }

    /**
     * FUNGSI TAB: Membagi tampilan tabel berdasarkan kategori tertentu.
     */
    public function getTabs(): array
    {
        return [
            // Tab Semua Data
            'semua' => Tab::make('Semua User')
                ->icon('heroicon-m-users')
                ->badge(User::count()),

            // Tab khusus Admin
            'admin' => Tab::make('Administrator')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'admin'))
                ->icon('heroicon-m-shield-check')
                ->badge(User::where('role', 'admin')->count())
                ->badgeColor('danger'),

            // Tab khusus Staff
            'staff' => Tab::make('Staff Operasional')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'staff'))
                ->icon('heroicon-m-briefcase')
                ->badge(User::where('role', 'staff')->count())
                ->badgeColor('success'),

            // Tab User Non-Aktif
            'inactive' => Tab::make('Non-Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->icon('heroicon-m-x-circle')
                ->badge(User::where('is_active', false)->count())
                ->badgeColor('gray'),
        ];
    }
}

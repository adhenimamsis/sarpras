<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Schema;

class MfkMaintenanceTable extends BaseWidget
{
    // Mengambil porsi layar penuh agar jadwal terlihat jelas
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = '🗓️ Jadwal Pemeliharaan Aset Rutin (MFK)';

    /**
     * Mengatur urutan widget di dashboard.
     * Diletakkan di bawah stats agar menjadi perhatian utama.
     */
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        // Cek keberadaan kolom untuk keamanan sistem
        $hasColumn = Schema::hasColumn('assets', 'tgl_maintenance_berikutnya');

        return $table
            ->query(
                $hasColumn
                    ? Asset::query()
                        ->with(['ruangan']) // Eager loading untuk cegah N+1 Query
                        ->whereNotNull('tgl_maintenance_berikutnya')
                        ->orderBy('tgl_maintenance_berikutnya', 'asc')
                    : Asset::query()->whereRaw('1 = 0')
            )
            ->emptyStateHeading($hasColumn ? 'Semua Aset Terpelihara' : 'Database Perlu Update')
            ->emptyStateDescription($hasColumn
                ? 'Tidak ada jadwal pemeliharaan rutin yang mendesak saat ini.'
                : 'Kolom maintenance belum terpasang. Silakan hubungi pengembang.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->columns([
                Tables\Columns\TextColumn::make('nama_alat')
                    ->label('Nama Aset')
                    ->description(fn (Asset $record): string => 'Merk: '.($record->merk ?? '-'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ruangan.nama_ruangan')
                    ->label('Lokasi')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tgl_maintenance_terakhir')
                    ->label('Servis Terakhir')
                    ->date('d/m/Y')
                    ->placeholder('N/A')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tgl_maintenance_berikutnya')
                    ->label('Jadwal Berikutnya')
                    ->date('d/m/Y')
                    ->sortable()
                    ->weight('bold')
                    ->fontFamily('mono')
                    ->color(static function ($state) {
                        if (! $state) {
                            return 'gray';
                        }

                        $days = now()->diffInDays(Carbon::parse($state), false);

                        if ($days < 0) {
                            return 'danger';
                        } // Sudah lewat
                        if ($days <= 7) {
                            return 'danger';
                        } // < 1 minggu (Mendesak)
                        if ($days <= 14) {
                            return 'warning';
                        } // < 2 minggu (Peringatan)

                        return 'success';
                    })
                    ->description(static function ($state) {
                        if (! $state) {
                            return null;
                        }

                        $days = now()->diffInDays(Carbon::parse($state), false);

                        if ($days < 0) {
                            return 'Terlambat '.abs($days).' Hari';
                        }
                        if ($days === 0) {
                            return 'Jadwal Hari Ini!';
                        }

                        return 'Sisa '.$days.' Hari Lagi';
                    }),

                Tables\Columns\TextColumn::make('kondisi')
                    ->label('Status Fisik')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->colors([
                        'success' => 'baik',
                        'warning' => 'rusak ringan',
                        'danger' => 'rusak berat',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('update_log')
                    ->label('Input Maintenance')
                    ->icon('heroicon-m-wrench-screwdriver')
                    ->color('warning')
                    ->url(fn (Asset $record): string => "/admin/maintenance-logs/create?asset_id={$record->id}")
                    ->button(),

                Tables\Actions\Action::make('view')
                    ->label('Aset')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn (Asset $record): string => "/admin/assets/{$record->id}"),
            ]);
    }
}

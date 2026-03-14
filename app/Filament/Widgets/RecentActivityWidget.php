<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    // Judul widget di dashboard
    protected static ?string $heading = 'Aktivitas Terbaru (Audit Trail)';

    // Penempatan: Urutan ke-3 di dashboard (di bawah stats)
    protected static ?int $sort = 3;

    // Lebar widget (Full screen)
    protected int|string|array $columnSpan = 'full';

    /**
     * Otomatis refresh data setiap 30 detik tanpa reload halaman.
     */
    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('H:i')
                    ->description(fn (ActivityLog $record): string => $record->created_at->diffForHumans())
                    ->width('15%'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-m-user-circle'),

                Tables\Columns\BadgeColumn::make('action')
                    ->label('Tindakan')
                    ->colors([
                        'success' => 'TAMBAH ASET',
                        'warning' => 'PERUBAHAN DATA',
                        'danger' => 'HAPUS ASET',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'TAMBAH ASET' => 'Entry Baru',
                        'PERUBAHAN DATA' => 'Update Data',
                        'HAPUS ASET' => 'Penghapusan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('asset_name')
                    ->label('Nama Aset / Sarpras')
                    ->wrap()
                    ->description(fn (ActivityLog $record): string => $record->action === 'PERUBAHAN DATA' ? 'Ada data yang diperbarui' : 'Log sistem tercatat'
                    ),
            ])
            ->actions([
                // Tombol cepat untuk lihat detail log
                Tables\Actions\Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->url(fn (ActivityLog $record): string => "/admin/activity-logs/{$record->id}"),
            ])
            ->paginated(false); // Matikan pagination agar rapi sebagai widget
    }
}

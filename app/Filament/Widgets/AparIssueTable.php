<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AparIssueTable extends BaseWidget
{
    protected static ?string $heading = '⚠️ Peringatan Dini Proteksi Kebakaran (APAR)';

    // Mengambil slot penuh agar tabel enak dibaca petugas sarpras
    protected int|string|array $columnSpan = 'full';

    // Refresh otomatis setiap 30 detik jika dashboard terbuka
    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->where('nama_alat', 'LIKE', '%APAR%')
                    ->where(function ($query) {
                        $query->where('tgl_kadaluarsa', '<', Carbon::now())
                            ->orWhereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'Perlu Cek']);
                    })
            )
            ->columns([
                TextColumn::make('nama_alat')
                    ->label('ID / Nama Unit')
                    ->description(fn (Asset $record): string => 'Merk: '.($record->merk ?? 'Tanpa Merk'))
                    ->searchable(),

                TextColumn::make('ruangan.nama_ruangan')
                    ->label('Lokasi Ruangan')
                    ->icon('heroicon-m-map-pin')
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('tgl_kadaluarsa')
                    ->label('Masa Berlaku')
                    ->date('d M Y')
                    ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'danger' : 'warning')
                    ->icon(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-clock')
                    ->sortable(),

                TextColumn::make('status_peringatan')
                    ->label('Status Masalah')
                    ->badge()
                    ->default(function (Asset $record) {
                        if ($record->tgl_kadaluarsa && Carbon::parse($record->tgl_kadaluarsa)->isPast()) {
                            return 'KADALUARSA';
                        }
                        if (in_array($record->kondisi, ['Rusak Ringan', 'Rusak Berat'])) {
                            return 'FISIK RUSAK';
                        }

                        return 'CEK TEKANAN';
                    })
                    ->color(fn (Asset $record): string => ($record->tgl_kadaluarsa && Carbon::parse($record->tgl_kadaluarsa)->isPast()) ? 'danger' : 'warning'
                    ),

                TextColumn::make('updated_at')
                    ->label('Cek Terakhir')
                    ->since()
                    ->dateTimeTooltip()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('update_monitoring')
                    ->label('Input Monitoring MFK')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->color('success')
                    ->url(fn (Asset $record): string => route('filament.admin.resources.monitoring-mfks.create', [
                        'asset_id' => $record->id,
                        'kategori' => 'Proteksi Kebakaran',
                    ]))
                    ->button(),
            ])
            ->emptyStateHeading('Semua APAR dalam Kondisi Siaga')
            ->emptyStateDescription('Tidak ditemukan unit yang kadaluarsa atau rusak saat ini.')
            ->emptyStateIcon('heroicon-o-shield-check');
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MfkResource\Pages;
use App\Models\MfkPemeliharaan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MfkResource extends Resource
{
    protected static ?string $model = MfkPemeliharaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Manajemen MFK';

    protected static ?string $navigationLabel = 'Pemeliharaan MFK';

    protected static ?string $modelLabel = 'Pemeliharaan MFK';

    protected static ?string $recordTitleAttribute = 'uraian_kegiatan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Asset & Jadwal')
                ->description('Pilih asset dan tentukan tanggal pemeliharaan.')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Forms\Components\Select::make('asset_id')
                        ->label('Nama Alat / Asset')
                        ->relationship('asset', 'nama_alat')
                        // Menampilkan nama alat beserta lokasi ruangannya agar tidak tertukar
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_alat} - ".($record->ruangan->nama_ruangan ?? 'Tanpa Ruangan'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('tgl_pemeliharaan')
                            ->label('Tanggal Servis')
                            ->default(now())
                            ->native(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Otomatis set jadwal berikutnya +3 bulan jika masih kosong
                                    $set('tgl_berikutnya', Carbon::parse($state)->addDays(90)->format('Y-m-d'));
                                }
                            }),
                        Forms\Components\DatePicker::make('tgl_berikutnya')
                            ->label('Jadwal Servis Kembali')
                            ->native(false)
                            ->helperText('Default: 90 hari (3 bulan) dari tanggal servis saat ini.'),
                    ]),
                ]),

            Forms\Components\Section::make('Detail Pekerjaan & Biaya')
                ->description('Catat uraian kegiatan teknis dan rincian biaya.')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    Forms\Components\Textarea::make('uraian_kegiatan')
                        ->label('Uraian Kegiatan')
                        ->placeholder('Contoh: Penggantian oli mesin genset, pembersihan filter indoor AC, atau penggantian kabel...')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Select::make('hasil_perbaikan')
                            ->label('Hasil Akhir')
                            ->options([
                                'Berfungsi' => '✅ Berfungsi',
                                'Rusak' => '❌ Rusak',
                                'Perlu Kalibrasi' => '⚠️ Perlu Kalibrasi',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('biaya')
                            ->label('Biaya Perbaikan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->placeholder('0'),

                        Forms\Components\TextInput::make('petugas')
                            ->label('Petugas/Vendor')
                            ->placeholder('Nama Teknisi / Nama PT Vendor')
                            ->required(),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.nama_alat')
                    ->label('Nama Asset')
                    ->searchable()
                    ->sortable()
                    ->description(fn (MfkPemeliharaan $record): string => '📍 '.($record->asset->ruangan->nama_ruangan ?? 'Lokasi tidak set')." | Teknisi: {$record->petugas}"
                    ),

                TextColumn::make('tgl_pemeliharaan')
                    ->label('Tgl Servis')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('tgl_berikutnya')
                    ->label('Next Servis')
                    ->date('d M Y')
                    ->badge()
                    ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'danger' : 'success')
                    ->icon(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle'),

                TextColumn::make('hasil_perbaikan')
                    ->label('Status Alat')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Berfungsi' => 'success',
                        'Rusak' => 'danger',
                        'Perlu Kalibrasi' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('biaya')
                    ->label('Biaya (Rp)')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total Anggaran')->money('IDR', locale: 'id')),
            ])
            ->defaultSort('tgl_pemeliharaan', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('hasil_perbaikan')
                    ->label('Hasil Akhir'),

                Tables\Filters\Filter::make('tgl_pemeliharaan')
                    ->form([
                        Forms\Components\DatePicker::make('dari')->label('Mulai Tanggal'),
                        Forms\Components\DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'], fn ($q, $date) => $q->whereDate('tgl_pemeliharaan', '>=', $date))
                            ->when($data['sampai'], fn ($q, $date) => $q->whereDate('tgl_pemeliharaan', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Cetak Laporan MFK')
                        ->icon('heroicon-o-document-text')
                        ->color('success'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMfks::route('/'),
            'create' => Pages\CreateMfk::route('/create'),
            'edit' => Pages\EditMfk::route('/{record}/edit'),
        ];
    }
}

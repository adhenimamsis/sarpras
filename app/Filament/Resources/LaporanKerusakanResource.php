<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKerusakanResource\Pages;
use App\Models\LaporanKerusakan;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LaporanKerusakanResource extends Resource
{
    protected static ?string $model = LaporanKerusakan::class;

    protected static ?string $navigationLabel = 'Laporan Kerusakan';

    protected static ?string $modelLabel = 'Laporan Kerusakan';

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationGroup = 'Manajemen Pemeliharaan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'Lapor')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Section::make('Informasi Laporan')
                        ->description('Pilih aset yang bermasalah dan isi identitas pelapor.')
                        ->columnSpan(2)
                        ->schema([
                            Forms\Components\Select::make('asset_id')
                                ->label('Aset / Alat / Bangunan')
                                ->relationship(
                                    name: 'asset',
                                    titleAttribute: 'nama_alat',
                                    // FIX: Eager loading ruangan untuk mencegah error lazy loading
                                    modifyQueryUsing: fn (Builder $query) => $query->with('ruangan')
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_alat} - ".($record->ruangan->nama_ruangan ?? 'Tanpa Ruangan'))
                                ->searchable()
                                ->preload()
                                ->required(),

                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('pelapor')
                                    ->label('Nama Pelapor')
                                    ->required(),

                                Forms\Components\DatePicker::make('tgl_lapor')
                                    ->label('Tanggal Lapor')
                                    ->default(now())
                                    ->native(false)
                                    ->required(),
                            ]),

                            Forms\Components\Textarea::make('deskripsi_kerusakan')
                                ->label('Detail Kerusakan')
                                ->rows(5)
                                ->required(),
                        ]),

                    Section::make('Progres Perbaikan')
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->options([
                                    'Lapor' => '🔴 Lapor (Baru)',
                                    'Proses' => '🟡 Dalam Proses',
                                    'Selesai' => '🟢 Selesai',
                                ])
                                ->default('Lapor')
                                ->required()
                                ->live(),

                            Forms\Components\DatePicker::make('tgl_selesai')
                                ->label('Tgl Selesai')
                                ->native(false)
                                ->hidden(fn (Forms\Get $get) => $get('status') !== 'Selesai')
                                ->required(fn (Forms\Get $get) => $get('status') === 'Selesai'),

                            Forms\Components\Textarea::make('tindakan_perbaikan')
                                ->label('Catatan Teknisi')
                                ->rows(5),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // FIX: Tambahkan eager loading di tabel agar tidak error saat load banyak data
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['asset.ruangan']))
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Lapor' => 'danger',
                        'Proses' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('asset.nama_alat')
                    ->label('Nama Aset')
                    ->description(fn (LaporanKerusakan $record): string => '📍 '.($record->asset->ruangan->nama_ruangan ?? 'Lokasi tidak set'))
                    ->searchable(),

                TextColumn::make('tgl_lapor')
                    ->label('Tgl Lapor')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['Lapor' => 'Lapor', 'Proses' => 'Proses', 'Selesai' => 'Selesai']),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKerusakans::route('/'),
            'create' => Pages\CreateLaporanKerusakan::route('/create'),
            'edit' => Pages\EditLaporanKerusakan::route('/{record}/edit'),
        ];
    }
}

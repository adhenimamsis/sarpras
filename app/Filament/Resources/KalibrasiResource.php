<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KalibrasiResource\Pages;
use App\Models\Kalibrasi;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class KalibrasiResource extends Resource
{
    protected static ?string $model = Kalibrasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Riwayat Kalibrasi';

    /**
     * Menu penutup rangkaian inventaris.
     */
    protected static ?int $navigationSort = 6;

    // Group disatukan di Manajemen Aset agar sidebar rapi
    protected static ?string $navigationGroup = 'Manajemen Aset';

    /**
     * Navigation Badge: Menghitung alat yang sudah expired.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('tgl_kadaluarsa', '<', now())->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pengujian Kalibrasi')
                    ->description('Masukkan detail sertifikat dan hasil pengujian alat untuk riwayat MFK.')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Select::make('asset_id')
                                ->label('Pilih Alat Kesehatan')
                                ->relationship('asset', 'nama_alat', fn (Builder $query) => $query->where('kategori_utilitas', 'Alkes'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->helperText('Hanya menampilkan kategori Alkes'),

                            Forms\Components\TextInput::make('no_sertifikat')
                                ->label('Nomor Sertifikat')
                                ->placeholder('Contoh: 001/KAL/2026')
                                ->required(),

                            Forms\Components\DatePicker::make('tgl_kalibrasi')
                                ->label('Tanggal Kalibrasi')
                                ->required()
                                ->live()
                                ->native(false)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        // Otomatis set kadaluarsa 1 tahun kedepan (Standar Kemenkes)
                                        $set('tgl_kadaluarsa', Carbon::parse($state)->addYear()->format('Y-m-d'));
                                    }
                                }),

                            Forms\Components\DatePicker::make('tgl_kadaluarsa')
                                ->label('Berlaku Sampai (Expired)')
                                ->helperText('Otomatis terisi 1 tahun dari tanggal kalibrasi')
                                ->required()
                                ->native(false),

                            Forms\Components\TextInput::make('pelaksana')
                                ->label('Lembaga Pelaksana')
                                ->placeholder('Contoh: BPFK / LPFK / PT. Kalibrasi')
                                ->required(),

                            Forms\Components\Select::make('hasil')
                                ->label('Hasil Pengujian')
                                ->options([
                                    'Layak' => '✅ Layak Pakai',
                                    'Tidak Layak' => '❌ Tidak Layak',
                                ])
                                ->required()
                                ->native(false),
                        ]),

                        Forms\Components\FileUpload::make('file_sertifikat')
                            ->label('Upload Scan Sertifikat (PDF)')
                            ->directory('sertifikat-kalibrasi')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.nama_alat')
                    ->label('Nama Alat')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Kalibrasi $record): string => 'SN: '.($record->asset->no_sertifikat ?? '-').' | Sertifikat: '.$record->no_sertifikat),

                TextColumn::make('tgl_kalibrasi')
                    ->label('Tgl Kalibrasi')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('hasil')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Layak' => 'success',
                        'Tidak Layak' => 'danger',
                        default => 'gray'
                    }),

                TextColumn::make('tgl_kadaluarsa')
                    ->label('Status Expired')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($state) => Carbon::parse($state)->isPast() ? 'danger' : 'success')
                    ->icon(fn ($state) => Carbon::parse($state)->isPast() ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                    ->description(fn ($record) => Carbon::parse($record->tgl_kadaluarsa)->diffForHumans()),

                TextColumn::make('pelaksana')
                    ->label('Lembaga')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tgl_kalibrasi', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('hasil')
                    ->label('Filter Hasil')
                    ->options([
                        'Layak' => 'Layak Pakai',
                        'Tidak Layak' => 'Tidak Layak',
                    ]),
                Tables\Filters\Filter::make('expired')
                    ->label('Tampilkan Yang Expired')
                    ->query(fn (Builder $query) => $query->where('tgl_kadaluarsa', '<', now()))
                    ->indicator('Sudah Expired'),
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
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Menambahkan widget statistik di bagian atas halaman list.
     */
    public static function getWidgets(): array
    {
        return [
            // Bos bisa buat widget custom di sini nanti
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKalibrasis::route('/'),
            'create' => Pages\CreateKalibrasi::route('/create'),
            'edit' => Pages\EditKalibrasi::route('/{record}/edit'),
        ];
    }
}

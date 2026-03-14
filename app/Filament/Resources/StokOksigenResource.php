<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StokOksigenResource\Pages;
use App\Models\StokOksigen;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StokOksigenResource extends Resource
{
    protected static ?string $model = StokOksigen::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationLabel = 'Persediaan Oksigen';

    /**
     * EKSEKUSI URUTAN MENU:
     * Diset ke 9 agar muncul secara linear di sidebar utama.
     */
    protected static ?int $navigationSort = 9;

    protected static ?string $navigationGroup = 'Logistik Medis';

    /**
     * Navigation Badge: Menampilkan sisa tabung besar (6m3) dan kecil (1m3) global.
     */
    public static function getNavigationBadge(): ?string
    {
        $stokBesar = static::getModel()::where('ukuran', '6m3')->latest()->value('stok_akhir') ?? 0;
        $stokKecil = static::getModel()::where('ukuran', '1m3')->latest()->value('stok_akhir') ?? 0;

        return "B: {$stokBesar} | K: {$stokKecil}";
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $stokBesar = static::getModel()::where('ukuran', '6m3')->latest()->value('stok_akhir') ?? 0;

        // Jika stok besar di bawah 2 tabung, badge berubah jadi merah
        return $stokBesar < 2 ? 'danger' : 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Mutasi Inventaris Oksigen')
                    ->description('Pencatatan masuk (Refill) dan keluar (Pakai) tabung oksigen.')
                    ->icon('heroicon-o-arrows-right-left')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Select::make('lokasi')
                                ->label('Unit Pengguna/Penyimpan')
                                ->options([
                                    'Gudang Utama' => '🏢 Gudang Utama',
                                    'IGD' => '🚑 IGD',
                                    'Rawat Inap' => '🛌 Rawat Inap',
                                    'Poli' => '🩺 Poli Umum',
                                    'PONED' => '👶 Ruang PONED',
                                ])
                                ->required()
                                ->native(false),

                            Forms\Components\Select::make('ukuran')
                                ->label('Kapasitas Tabung')
                                ->options([
                                    '1m3' => '📦 Tabung Kecil (1m3)',
                                    '6m3' => '🏭 Tabung Besar (6m3)',
                                ])
                                ->required()
                                ->native(false),

                            Forms\Components\TextInput::make('jumlah_masuk')
                                ->label('Jumlah Masuk (Refill)')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->suffix('Tabung')
                                ->required(),

                            Forms\Components\TextInput::make('jumlah_keluar')
                                ->label('Jumlah Keluar (Pakai)')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->suffix('Tabung')
                                ->required(),

                            Forms\Components\TextInput::make('petugas')
                                ->label('Petugas Pencatat')
                                ->default(fn () => Auth::user()?->name)
                                ->readOnly()
                                ->required(),
                        ]),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Detail Transaksi')
                            ->placeholder('Contoh: Pengiriman dari PT. Gas, atau Penggunaan Pasien Ny. X di IGD...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Transaksi')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('ukuran')
                    ->label('Ukuran')
                    ->alignCenter(),

                TextColumn::make('jumlah_masuk')
                    ->label('Masuk (+)')
                    ->color('success')
                    ->weight('bold')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "+{$state}" : '-')
                    ->alignCenter(),

                TextColumn::make('jumlah_keluar')
                    ->label('Keluar (-)')
                    ->color('danger')
                    ->weight('bold')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "-{$state}" : '-')
                    ->alignCenter(),

                TextColumn::make('stok_akhir')
                    ->label('Sisa Stok')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 1 => 'danger',   // Sangat Kritis
                        $state <= 3 => 'warning',  // Waspada
                        default => 'success',      // Aman
                    })
                    ->icon(fn ($state) => match (true) {
                        $state <= 1 => 'heroicon-m-fire',
                        $state <= 3 => 'heroicon-m-exclamation-circle',
                        default => 'heroicon-m-check-circle',
                    })
                    ->alignCenter(),

                TextColumn::make('petugas')
                    ->label('Petugas')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('ukuran')
                    ->label('Filter Ukuran'),
                Tables\Filters\SelectFilter::make('lokasi')
                    ->label('Filter Lokasi'),
                Tables\Filters\Filter::make('stok_kritis')
                    ->label('Tampilkan Stok Kritis')
                    ->query(fn (Builder $query) => $query->where('stok_akhir', '<=', 2)),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStokOksigens::route('/'),
            'create' => Pages\CreateStokOksigen::route('/create'),
            'edit' => Pages\EditStokOksigen::route('/{record}/edit'),
        ];
    }
}

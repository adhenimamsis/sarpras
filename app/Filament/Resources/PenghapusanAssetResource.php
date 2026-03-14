<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenghapusanAssetResource\Pages;
use App\Models\PenghapusanAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenghapusanAssetResource extends Resource
{
    protected static ?string $model = PenghapusanAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';

    protected static ?string $navigationLabel = 'Penghapusan Aset';

    // Disatukan ke Manajemen Aset agar sidebar tidak berantakan
    protected static ?string $navigationGroup = 'Manajemen Aset';

    protected static ?int $navigationSort = 10;

    /**
     * HAK AKSES:
     * Menjaga keamanan data agar tidak sembarang orang bisa menghapus inventaris negara.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Proses Penghapusan Aset (BAP)')
                ->icon('heroicon-o-document-minus')
                ->description('Peringatan: Aset yang diproses di sini akan otomatis berubah status kondisinya menjadi "Dihapuskan" pada master data KIB.')
                ->schema([
                    Forms\Components\Select::make('asset_id')
                        ->label('Pilih Aset / Alat')
                        ->relationship('asset', 'nama_alat', function (Builder $query) {
                            // Hanya tampilkan aset yang belum dihapus
                            return $query->where('kondisi', '!=', 'Dihapuskan');
                        })
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_alat} - Reg: {$record->no_register} ({$record->merk})")
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('tgl_penghapusan')
                            ->label('Tanggal Penghapusan')
                            ->default(now())
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('no_sk')
                            ->label('Nomor SK / Berita Acara')
                            ->placeholder('Contoh: 800/BAP-ASSET/II/2026')
                            ->required(),

                        Forms\Components\Select::make('alasan')
                            ->label('Alasan Penghapusan')
                            ->options([
                                'Rusak Berat' => '🛠️ Rusak Berat (Tidak Ekonomis)',
                                'Hibah' => '🎁 Hibah ke Instansi Lain',
                                'Dijual' => '💰 Dijual/Lelang',
                                'Hilang' => '🔍 Hilang / Dicuri',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('metode')
                            ->label('Metode Eksekusi')
                            ->options([
                                'Pemusnahan' => '🔥 Pemusnahan Fisik',
                                'Pemindahtanganan' => '🤝 Pemindahtanganan',
                                'Penjualan' => '⚖️ Penjualan/Lelang Umum',
                            ])
                            ->required()
                            ->native(false),
                    ]),

                    Forms\Components\FileUpload::make('foto_bukti')
                        ->label('Foto Bukti / Dokumen SK (Scan)')
                        ->directory('penghapusan-aset')
                        ->image()
                        ->imageEditor()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(4096)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('keterangan')
                        ->label('Kronologi / Catatan Tambahan')
                        ->placeholder('Contoh: Barang sudah tidak bisa diperbaiki karena sparepart tidak tersedia...')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tgl_penghapusan')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('asset.nama_alat')
                    ->label('Nama Aset')
                    ->description(fn (PenghapusanAsset $record): string => 'Reg: '.($record->asset->no_register ?? '-').' | SK: '.($record->no_sk ?? '-')
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('alasan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Rusak Berat' => 'danger',
                        'Hibah' => 'info',
                        'Dijual' => 'success',
                        'Hilang' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('metode')
                    ->label('Metode')
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('foto_bukti')
                    ->label('Bukti')
                    ->circular(),
            ])
            ->defaultSort('tgl_penghapusan', 'desc')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make('print')
                        ->label('Cetak BAP')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        // Pastikan bos sudah buat route 'penghapusan.print'
                        ->url(fn (PenghapusanAsset $record): string => route('penghapusan.print', $record), shouldOpenInNewTab: true)
                        ->visible(fn (): bool => auth()->user()?->can('reports.view.sensitive') ?? false),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (PenghapusanAsset $record) {
                            // JAGA-JAGA: Jika data penghapusan dihapus, kembalikan status aset ke 'Baik'
                            $record->asset->update(['kondisi' => 'Baik']);
                        }),
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
            'index' => Pages\ListPenghapusanAssets::route('/'),
            'create' => Pages\CreatePenghapusanAsset::route('/create'),
            'edit' => Pages\EditPenghapusanAsset::route('/{record}/edit'),
        ];
    }
}

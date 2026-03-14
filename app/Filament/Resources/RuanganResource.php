<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RuanganResource\Pages;
use App\Models\Ruangan;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RuanganResource extends Resource
{
    protected static ?string $model = Ruangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Data Ruangan & Gedung';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        // SECTION 1: IDENTITAS RUANGAN
                        Section::make('Informasi Dasar Ruangan')
                            ->description('Detail penamaan dan posisi ruangan di lingkungan Puskesmas.')
                            ->columnSpan(2)
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('nama_ruangan')
                                        ->label('Nama Ruangan / Poli')
                                        ->placeholder('Contoh: Poli KIA / Ruang MTBS')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('kode_ruangan')
                                        ->label('Kode Ruangan')
                                        ->placeholder('Contoh: R-001')
                                        ->unique(ignoreRecord: true),

                                    Select::make('gedung')
                                        ->label('Gedung / Bangunan')
                                        ->options([
                                            'Gedung Utama' => '🏢 Gedung Utama',
                                            'Gedung PONED' => '👶 Gedung PONED',
                                            'Gedung Rawat Inap' => '🛌 Gedung Rawat Inap',
                                            'Gedung Farmasi' => '💊 Gedung Farmasi',
                                            'Gedung Penunjang' => '🛠️ Gedung Penunjang',
                                        ])
                                        ->searchable()
                                        ->required()
                                        ->native(false),

                                    TextInput::make('lantai')
                                        ->label('Posisi Lantai')
                                        ->placeholder('Contoh: Lantai 2')
                                        ->default('Lantai 1'),
                                ]),

                                Textarea::make('keterangan')
                                    ->label('Catatan Lokasi')
                                    ->placeholder('Misal: Bersebelahan dengan ruang administrasi')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),

                        // SECTION 3: VISUAL & DENAH
                        Section::make('Visualisasi Denah')
                            ->description('Upload denah atau layout.')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('foto_denah')
                                    ->label('Foto Lokasi / Denah')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(4096)
                                    ->directory('denah-ruangan')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['1:1', '16:9'])
                                    ->helperText('Gunakan format JPG/PNG.'),
                            ]),

                        // SECTION 2: LEGALITAS TANAH & BANGUNAN
                        Section::make('Detail Aset Lahan & Bangunan (ASPAK)')
                            ->description('Informasi legalitas yang sinkron dengan pelaporan ASPAK.')
                            ->icon('heroicon-m-document-text')
                            ->columnSpanFull()
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('status_tanah')
                                        ->label('Status Penguasaan Lahan')
                                        ->placeholder('Hak Pakai / Milik Pemda'),

                                    TextInput::make('no_sertifikat')
                                        ->label('Nomor Sertifikat Lahan')
                                        ->placeholder('Contoh: SHP 12.03.xxx'),

                                    TextInput::make('asal_usul')
                                        ->label('Sumber Perolehan')
                                        ->placeholder('APBD / Hibah / APBN'),

                                    TextInput::make('penggunaan')
                                        ->label('Fungsi Penggunaan')
                                        ->placeholder('Pelayanan Medis / Kantor'),
                                ]),

                                Textarea::make('alamat_lokasi')
                                    ->label('Alamat Lengkap Unit')
                                    ->placeholder('Masukkan alamat lengkap jika lokasi berbeda gedung utama...')
                                    ->columnSpanFull(),
                            ])->collapsed(), // Default ditutup agar form tidak terlalu panjang
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_denah')
                    ->label('Foto')
                    ->circular()
                    ->disk('public'),

                TextColumn::make('nama_ruangan')
                    ->label('Nama Ruangan')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Ruangan $record): string => 'Kode: '.($record->kode_ruangan ?? '-')),

                TextColumn::make('gedung')
                    ->label('Gedung')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('lantai')
                    ->label('Lantai')
                    ->alignCenter(),

                TextColumn::make('assets_count')
                    ->counts('assets')
                    ->label('Total Barang')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->icon('heroicon-m-cube')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Tgl Update')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('gedung', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('gedung')
                    ->options([
                        'Gedung Utama' => 'Gedung Utama',
                        'Gedung PONED' => 'Gedung PONED',
                        'Gedung Rawat Inap' => 'Gedung Rawat Inap',
                        'Gedung Farmasi' => 'Gedung Farmasi',
                    ])
                    ->label('Filter Per Gedung'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make('cetak_kir')
                        ->label('Cetak KIR (Kartu Inventaris Ruangan)')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->url(fn (Ruangan $record): string => route('cetak.ruangan', $record->id), shouldOpenInNewTab: true),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
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
        return [
            // Sangat disarankan menambahkan AssetsRelationManager di sini bos!
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRuangans::route('/'),
            'create' => Pages\CreateRuangan::route('/create'),
            'edit' => Pages\EditRuangan::route('/{record}/edit'),
        ];
    }
}

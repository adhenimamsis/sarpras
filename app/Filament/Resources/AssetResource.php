<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    /**
     * PENTING: NavigationIcon dihapus di sini karena sudah dipasang di
     * AdminPanelProvider pada bagian NavigationGroup untuk menghindari error UI.
     */
    protected static ?string $navigationLabel = 'Data Sarpras (ASPAK/KIB)';

    protected static ?string $navigationGroup = 'Sarana & Prasarana';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        // --- SECTION KIRI: IDENTITAS ASET ---
                        Section::make('Identitas Aset (Standard KIB A-F)')
                            ->description('Input data sesuai dengan dokumen Kartu Inventaris Barang Puskesmas Bendan.')
                            ->columnSpan(2)
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('kategori_kib')
                                        ->label('Jenis KIB')
                                        ->options([
                                            'KIB_A' => 'KIB A (Tanah)',
                                            'KIB_B' => 'KIB B (Peralatan & Mesin)',
                                            'KIB_C' => 'KIB C (Gedung & Bangunan)',
                                            'KIB_D' => 'KIB D (Jalan, Irigasi & Jaringan)',
                                            'KIB_E' => 'KIB E (Aset Tetap Lainnya)',
                                            'KIB_F' => 'KIB F (Konstruksi Dalam Pengerjaan)',
                                        ])
                                        ->required()
                                        ->live()
                                        ->placeholder('Pilih Jenis KIB')
                                        ->native(false),

                                    TextInput::make('nama_alat')
                                        ->label('Nama / Jenis Barang')
                                        ->placeholder('Contoh: PC Unit / Jaringan IPAL')
                                        ->required(),

                                    TextInput::make('kode_aspak')
                                        ->label('Kode Barang / ASPAK')
                                        ->placeholder('02.10.01.02.001')
                                        ->required(),

                                    TextInput::make('no_register')
                                        ->label('Nomor Register')
                                        ->placeholder('0001')
                                        ->required(),

                                    TextInput::make('merk')
                                        ->label(fn (Get $get) => in_array($get('kategori_kib') ?? '', ['KIB_A', 'KIB_C', 'KIB_D']) ? 'Konstruksi / Hak' : 'Merk / Type')
                                        ->placeholder('Fiber Optik / LG / Beton')
                                        ->hidden(fn (Get $get) => ($get('kategori_kib') ?? '') === 'KIB_F'),

                                    TextInput::make('no_sertifikat')
                                        ->label(fn (Get $get) => ($get('kategori_kib') ?? '') === 'KIB_B' ? 'No. Seri / Pabrik' : 'No. Sertifikat / Dokumen')
                                        ->placeholder('SN: 12345 / IMB No. 10')
                                        ->visible(fn (Get $get) => ($get('kategori_kib') ?? '') !== 'KIB_F'),

                                    TextInput::make('harga_perolehan')
                                        ->label('Nilai Perolehan')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->required(),

                                    TextInput::make('tahun_perolehan')
                                        ->numeric()
                                        ->label('Tahun Perolehan')
                                        ->required(),

                                    Select::make('kondisi')
                                        ->label('Kondisi Barang')
                                        ->options([
                                            'Baik' => 'B (Baik)',
                                            'Rusak Ringan' => 'RR (Rusak Ringan)',
                                            'Rusak Berat' => 'RB (Rusak Berat)',
                                        ])
                                        ->default('Baik')
                                        ->required()
                                        ->native(false),

                                    Select::make('asal_usul')
                                        ->label('Asal Usul')
                                        ->options([
                                            'Pembelian' => 'Pembelian',
                                            'Hibah' => 'Hibah',
                                            'APBD' => 'APBD',
                                            'APBN' => 'APBN',
                                        ])
                                        ->native(false),
                                ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('alamat')
                                            ->label('Letak / Lokasi / Alamat')
                                            ->placeholder('Ruang Poli / Jl. Slamet No. 2')
                                            ->columnSpanFull(),

                                        TextInput::make('luas_meter')
                                            ->label('Luas / Panjang (m / m2)')
                                            ->numeric()
                                            ->suffix('m')
                                            ->visible(fn (Get $get) => in_array($get('kategori_kib') ?? '', ['KIB_A', 'KIB_C', 'KIB_D'])),

                                        TextInput::make('konstruksi')
                                            ->label('Detail Konstruksi / Jaringan')
                                            ->placeholder('Beton / Aspal / PVC')
                                            ->visible(fn (Get $get) => in_array($get('kategori_kib') ?? '', ['KIB_C', 'KIB_D'])),
                                    ]),

                                FileUpload::make('foto')
                                    ->image()
                                    ->label('Foto Fisik Aset')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(4096)
                                    ->directory('assets-foto')
                                    ->columnSpanFull(),
                            ]),

                        // --- SECTION KANAN: KEPATUHAN & ASPAK ---
                        Section::make('Kepatuhan & Akuntansi')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('nilai_buku_display')
                                    ->label('Estimasi Nilai Buku')
                                    ->placeholder(fn (Get $get) => 'Rp '.number_format((float) ($get('harga_perolehan') ?? 0), 0, ',', '.'))
                                    ->disabled()
                                    ->dehydrated(false),

                                Select::make('kategori_utilitas')
                                    ->label('Kategori Sistem (ASPAK)')
                                    ->options([
                                        'Alkes' => '🩺 Alat Kesehatan',
                                        'Non-Alkes' => '📦 Non-Alkes',
                                        'Utilitas' => '⚡ Utilitas (Listrik/Air)',
                                        'IPAL' => '🧪 Jaringan IPAL',
                                        'Jaringan' => '🌐 Jaringan (Telp/Internet)',
                                        'Bangunan' => '🏢 Bangunan',
                                        'Ambulans' => '🚑 Ambulans / Kendaraan',
                                        'Alat_Kantor' => '🖨️ Alat Kantor',
                                        'Lainnya' => '⚙️ Lainnya',
                                    ])
                                    ->required()
                                    ->native(false),

                                Toggle::make('status_kalibrasi')
                                    ->label('Wajib Kalibrasi/Uji')
                                    ->live(),

                                DatePicker::make('tgl_maintenance_terakhir')
                                    ->label('Tgl Pemeliharaan Terakhir'),

                                DatePicker::make('tgl_maintenance_berikutnya')
                                    ->label('Jadwal Berikutnya'),

                                FileUpload::make('sertifikat')
                                    ->label('Scan Sertifikat (PDF)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(5120)
                                    ->directory('assets-sertifikat'),

                                Textarea::make('catatan')
                                    ->label('Keterangan')
                                    ->rows(3),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kategori_kib')
                    ->label('KIB')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'KIB_A' => 'success',
                        'KIB_B' => 'info',
                        'KIB_C' => 'warning',
                        'KIB_D' => 'danger',
                        'KIB_E' => 'gray',
                        default => 'primary',
                    })
                    ->sortable(),

                TextColumn::make('nama_alat')
                    ->label('Nama Aset')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Asset $record): string => "Reg: {$record->no_register} | Kode: {$record->kode_aspak}"),

                TextColumn::make('harga_perolehan')
                    ->label('Nilai Awal')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('kondisi')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Baik', 'B' => 'success',
                        'Rusak Ringan', 'RR', 'KB' => 'warning',
                        'Rusak Berat', 'RB' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('kategori_kib')->label('Jenis KIB'),
                SelectFilter::make('kondisi'),
                SelectFilter::make('kategori_utilitas')->label('Kategori ASPAK'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Export Format KIB (Excel)')
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename('Rekap_Aset_Bendan_'.date('Y-m-d')),
                        ]),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringMfkResource\Pages;
use App\Models\Asset;
use App\Models\MonitoringMfk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MonitoringMfkResource extends Resource
{
    protected static ?string $model = MonitoringMfk::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationLabel = 'Monitoring Harian MFK';

    protected static ?string $modelLabel = 'Monitoring MFK';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Manajemen MFK';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas Pemeriksaan')
                ->icon('heroicon-o-identification')
                ->description('Pencatatan harian/rutin kondisi fasilitas Puskesmas.')
                ->schema([
                    Forms\Components\Select::make('jenis_utilitas')
                        ->label('Objek Monitoring')
                        ->options([
                            'Listrik' => '⚡ Listrik (Genset/PLN)',
                            'Air' => '💧 Air Bersih',
                            'Gas Medik' => '🧪 Gas Medik/Oksigen',
                            'APAR' => '🧯 APAR',
                            'IPAL' => '🧪 Limbah IPAL',
                            'Bangunan' => '🏢 Fasilitas/Gedung',
                        ])
                        ->required()
                        ->native(false)
                        ->live()
                        ->prefix('Cek:'),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('tgl_cek')
                            ->label('Tanggal')
                            ->default(now())
                            ->native(false)
                            ->required(),

                        Forms\Components\TimePicker::make('waktu_cek')
                            ->label('Jam Pemeriksaan')
                            ->default(now())
                            ->required(),
                    ]),
                ])->columns(2),

            Forms\Components\Section::make('Detail Temuan Lapangan')
                ->icon('heroicon-o-magnifying-glass-circle')
                ->schema([
                    // --- 1. DETAIL LISTRIK ---
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('tegangan_v')
                            ->label('Tegangan (Volt)')
                            ->numeric()
                            ->suffix('Volt')
                            ->placeholder('220'),
                        Forms\Components\TextInput::make('beban_a')
                            ->label('Beban (Ampere)')
                            ->numeric()
                            ->suffix('A'),
                    ])->visible(fn (Get $get) => $get('jenis_utilitas') === 'Listrik'),

                    // --- 2. DETAIL AIR ---
                    Forms\Components\CheckboxList::make('kondisi_air')
                        ->label('Kualitas Air Bersih')
                        ->options([
                            'jernih' => 'Jernih',
                            'tidak_berbau' => 'Tidak Berbau',
                            'pompa_normal' => 'Pompa Normal',
                            'tandon_penuh' => 'Tandon Terisi',
                        ])
                        ->columns(2)
                        ->visible(fn (Get $get) => $get('jenis_utilitas') === 'Air'),

                    // --- 3. DETAIL GAS MEDIK ---
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('tekanan_oksigen')
                                ->label('Tekanan PSI')
                                ->numeric()
                                ->suffix('PSI'),
                            Forms\Components\Select::make('stok_cadangan')
                                ->label('Stok Tabung')
                                ->options([
                                    'Tersedia' => 'Tersedia',
                                    'Menipis' => 'Menipis',
                                    'Kosong' => 'Kosong',
                                ]),
                        ])
                        ->visible(fn (Get $get) => $get('jenis_utilitas') === 'Gas Medik'),

                    // --- 4. DETAIL APAR ---
                    Forms\Components\Repeater::make('detail_apar')
                        ->label('Checklist Unit APAR')
                        ->schema([
                            Forms\Components\Select::make('nama_apar')
                                ->label('Pilih Unit APAR')
                                ->options(fn () => Asset::where('nama_alat', 'LIKE', '%APAR%')->pluck('nama_alat', 'nama_alat'))
                                ->searchable()
                                ->required(),
                            Forms\Components\Select::make('kondisi')
                                ->label('Tekanan & Fisik')
                                ->options([
                                    'Normal' => '✅ Hijau (Normal)',
                                    'Lemah' => '⚠️ Lemah',
                                    'Rusak' => '❌ Rusak/Bocor',
                                ])->required(),
                        ])
                        ->visible(fn (Get $get) => $get('jenis_utilitas') === 'APAR')
                        ->columns(2)->columnSpanFull(),

                    // --- 5. DETAIL IPAL ---
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Toggle::make('inlet_lancar')->label('Inlet Lancar')->default(true),
                        Forms\Components\Toggle::make('aerasi_aktif')->label('Blower Aktif')->default(true),
                        Forms\Components\Toggle::make('outlet_jernih')->label('Outlet Jernih')->default(true),
                    ])->visible(fn (Get $get) => $get('jenis_utilitas') === 'IPAL'),

                    // --- 6. DETAIL BANGUNAN ---
                    Forms\Components\CheckboxList::make('check_bangunan')
                        ->label('Pemeriksaan Fisik Gedung')
                        ->options([
                            'atap' => 'Atap & Plafon (Tidak Bocor)',
                            'dinding' => 'Dinding (Tidak Retak/Lembab)',
                            'lantai' => 'Lantai (Bersih & Utuh)',
                            'pintu' => 'Pintu & Jendela (Berfungsi)',
                        ])
                        ->visible(fn (Get $get) => $get('jenis_utilitas') === 'Bangunan'),
                ]),

            Forms\Components\Section::make('Hasil & Rekomendasi')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('status')
                            ->label('Kesimpulan Kondisi')
                            ->options([
                                'Normal' => '✅ Normal / Aman',
                                'Gangguan' => '⚠️ Ada Gangguan',
                                'Perbaikan' => '🛠️ Butuh Perbaikan Segera',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('petugas')
                            ->label('Petugas Pemeriksa')
                            ->default(fn () => Auth::user()?->name)
                            ->required()
                            ->readOnly(),
                    ]),

                    Forms\Components\Textarea::make('parameter_cek')
                        ->label('Catatan Temuan / Deskripsi')
                        ->placeholder('Sebutkan jika ada temuan khusus di lapangan...')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('foto_bukti')
                        ->label('Foto Dokumentasi')
                        ->image()
                        ->imageEditor()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(4096)
                        ->directory('mfk-monitoring')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('keterangan')
                        ->label('Rencana Tindak Lanjut (RTL)')
                        ->placeholder('Apa yang harus dilakukan untuk mengatasi temuan?')
                        ->required(fn (Get $get) => $get('status') !== 'Normal')
                        ->visible(fn (Get $get) => $get('status') !== 'Normal')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tgl_cek')
                    ->label('Tanggal Cek')
                    ->date('d/m/Y')
                    ->sortable()
                    ->description(fn ($record) => 'Jam: '.$record->waktu_cek),

                TextColumn::make('jenis_utilitas')
                    ->label('Objek')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Normal' => 'success',
                        'Gangguan' => 'danger',
                        'Perbaikan' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('parameter_cek')
                    ->label('Temuan')
                    ->limit(30)
                    ->searchable(),

                ImageColumn::make('foto_bukti')
                    ->label('Foto')
                    ->circular()
                    ->disk('public'),

                TextColumn::make('petugas')
                    ->label('Pemeriksa')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('tgl_cek', 'desc')
            ->filters([
                SelectFilter::make('jenis_utilitas')->label('Filter Objek'),
                SelectFilter::make('status')->label('Filter Kondisi'),
                Tables\Filters\Filter::make('tgl_cek')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['created_from'], fn ($q, $date) => $q->whereDate('tgl_cek', '>=', $date))
                        ->when($data['created_until'], fn ($q, $date) => $q->whereDate('tgl_cek', '<=', $date))),
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
            'index' => Pages\ListMonitoringMfks::route('/'),
            'create' => Pages\CreateMonitoringMfk::route('/create'),
            'edit' => Pages\EditMonitoringMfk::route('/{record}/edit'),
        ];
    }
}

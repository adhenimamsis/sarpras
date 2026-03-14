<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UtilityLogResource\Pages;
use App\Models\UtilityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UtilityLogResource extends Resource
{
    protected static ?string $model = UtilityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Monitoring Utilitas';

    protected static ?string $pluralModelLabel = 'Log Utilitas';

    protected static ?string $navigationGroup = 'Manajemen Aset';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Pencatatan Konsumsi Utilitas')
                ->icon('heroicon-o-pencil-square')
                ->description('Input angka meteran harian atau sisa stok operasional Puskesmas.')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('tgl_catat')
                            ->label('Tanggal Pengecekan')
                            ->default(now())
                            ->native(false)
                            ->required(),

                        Forms\Components\Select::make('jenis')
                            ->label('Kategori Utilitas')
                            ->options([
                                'listrik' => '⚡ Listrik (kWh)',
                                'air' => '💧 Air Bersih (m3)',
                                'ipal' => '🧪 IPAL (m3)',
                                'solar' => '⛽ Solar Genset (Liter)',
                                'gas_medik' => '🧪 Gas Medik (Bar/PSI)',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),
                    ]),

                    Forms\Components\TextInput::make('nama_meteran')
                        ->label('Lokasi / Titik Ukur')
                        ->placeholder(fn (Get $get): string => match ($get('jenis')) {
                            'gas_medik' => 'Contoh: Tabung Sentral / Kamar OK',
                            'solar' => 'Contoh: Tangki Cadangan Genset',
                            default => 'Contoh: Gedung Utama / Meteran PLN',
                        })
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('angka_meteran')
                            ->label(fn (Get $get): string => match ($get('jenis')) {
                                'solar' => 'Sisa Stok Solar',
                                'gas_medik' => 'Tekanan Gas O2',
                                default => 'Angka Meteran Terakhir',
                            })
                            ->numeric()
                            ->required()
                            ->prefixIcon(fn (Get $get): string => match ($get('jenis')) {
                                'listrik' => 'heroicon-m-bolt',
                                'air' => 'heroicon-m-beaker',
                                'solar' => 'heroicon-m-fire',
                                'gas_medik' => 'heroicon-m-variable',
                                default => 'heroicon-m-presentation-chart-line',
                            })
                            ->suffix(fn (Get $get): string => match ($get('jenis')) {
                                'listrik' => 'kWh',
                                'air', 'ipal' => 'm3',
                                'solar' => 'Liter',
                                'gas_medik' => 'Bar/PSI',
                                default => '',
                            }),

                        Forms\Components\TextInput::make('petugas')
                            ->label('Petugas Pemeriksa')
                            ->default(fn () => auth()->user()->name)
                            ->readOnly()
                            ->required(),
                    ]),

                    Forms\Components\Textarea::make('catatan')
                        ->label('Keterangan / Temuan Khusus')
                        ->placeholder('Contoh: Meteran sedikit berembun, atau pengisian solar full tangki.')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tgl_catat')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'listrik' => 'warning',
                        'air' => 'info',
                        'ipal' => 'success',
                        'solar' => 'danger',
                        'gas_medik' => 'primary',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'gas_medik' => 'Gas Medik (O2)',
                        'ipal' => 'Limbah (IPAL)',
                        default => ucfirst($state)
                    }),

                Tables\Columns\TextColumn::make('nama_meteran')
                    ->label('Lokasi Titik')
                    ->searchable()
                    ->description(fn (UtilityLog $record) => $record->catatan ? 'Catatan: '.\Illuminate\Support\Str::limit($record->catatan, 30) : null),

                Tables\Columns\TextColumn::make('angka_meteran')
                    ->label('Posisi Angka')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->alignRight()
                    ->weight('bold')
                    ->suffix(fn (UtilityLog $record): string => match ($record->jenis) {
                        'listrik' => ' kWh',
                        'air', 'ipal' => ' m3',
                        'solar' => ' Ltr',
                        'gas_medik' => ' Bar',
                        default => '',
                    }),

                Tables\Columns\TextColumn::make('petugas')
                    ->label('Petugas')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tgl_catat', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Filter Kategori Utilitas')
                    ->options([
                        'listrik' => 'Listrik',
                        'air' => 'Air Bersih',
                        'ipal' => 'IPAL',
                        'solar' => 'Solar Genset',
                        'gas_medik' => 'Gas Medik',
                    ]),
                Tables\Filters\Filter::make('tgl_catat')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['created_from'], fn ($q, $date) => $q->whereDate('tgl_catat', '>=', $date))
                        ->when($data['created_until'], fn ($q, $date) => $q->whereDate('tgl_catat', '<=', $date))),
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
            'index' => Pages\ListUtilityLogs::route('/'),
            'create' => Pages\CreateUtilityLog::route('/create'),
            'edit' => Pages\EditUtilityLog::route('/{record}/edit'),
        ];
    }
}

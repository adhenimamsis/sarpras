<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemeliharaanResource\Pages;
use App\Models\Pemeliharaan;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PemeliharaanResource extends Resource
{
    protected static ?string $model = Pemeliharaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Pemeliharaan Rutin';

    /**
     * EKSEKUSI URUTAN MENU:
     * Diset ke 5 agar muncul setelah Data Ruangan.
     */
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Manajemen Pemeliharaan';

    /**
     * Menampilkan jumlah pemeliharaan yang BELUM selesai di sidebar.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'Terjadwal')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Kegiatan Pemeliharaan')
                    ->description('Catat jadwal dan realisasi pemeliharaan aset Puskesmas.')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Select::make('asset_id')
                                ->label('Aset / Alat Kesehatan')
                                ->relationship('asset', 'nama_alat')
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_alat} - ".($record->ruangan->nama_ruangan ?? 'Lokasi tidak set'))
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\TextInput::make('kegiatan')
                                ->label('Nama Kegiatan')
                                ->placeholder('Contoh: Kalibrasi Internal / Pembersihan Filter / Cek Oli')
                                ->required(),

                            Forms\Components\DatePicker::make('tgl_jadwal')
                                ->label('Jadwal Pelaksanaan')
                                ->native(false)
                                ->required(),

                            Forms\Components\Select::make('status')
                                ->options([
                                    'Terjadwal' => '📅 Terjadwal',
                                    'Selesai' => '✅ Selesai',
                                ])
                                ->default('Terjadwal')
                                ->required()
                                ->native(false)
                                ->live(),

                            Forms\Components\TextInput::make('petugas')
                                ->label('Petugas Pelaksana')
                                ->placeholder('Nama teknisi / petugas vendor')
                                ->required(),
                        ]),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Catatan Hasil Pemeliharaan')
                            ->placeholder('Jelaskan kondisi alat setelah dipelihara atau part yang diganti...')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tgl_jadwal')
                    ->label('Jadwal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable()
                    ->color(fn ($record) => $record->status === 'Terjadwal' && $record->tgl_jadwal < now() ? 'danger' : null)
                    ->weight(fn ($record) => $record->status === 'Terjadwal' && $record->tgl_jadwal < now() ? 'bold' : 'normal'),

                TextColumn::make('asset.nama_alat')
                    ->label('Aset & Lokasi')
                    ->description(fn ($record) => "Kegiatan: {$record->kegiatan} | 📍 ".($record->asset->ruangan->nama_ruangan ?? '-'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('petugas')
                    ->label('Teknisi')
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Terjadwal' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Terjadwal' => 'heroicon-o-clock',
                        'Selesai' => 'heroicon-o-check-badge',
                        default => 'heroicon-o-question-mark-circle',
                    }),
            ])
            ->defaultSort('tgl_jadwal', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Terjadwal' => 'Terjadwal',
                        'Selesai' => 'Selesai',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Melewati Jadwal')
                    ->query(fn (Builder $query): Builder => $query->where('tgl_jadwal', '<', now())->where('status', 'Terjadwal')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // FITUR CEPAT: Tandai Selesai tanpa buka halaman edit
                    Action::make('markAsSelesai')
                        ->label('Set Selesai')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->hidden(fn (Pemeliharaan $record): bool => $record->status === 'Selesai')
                        ->requiresConfirmation()
                        ->action(function (Pemeliharaan $record) {
                            $record->update([
                                'status' => 'Selesai',
                                'tgl_jadwal' => now(), // Update ke tanggal realisasi
                            ]);

                            Notification::make()
                                ->title('Pemeliharaan Selesai')
                                ->success()
                                ->send();
                        }),

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
            'index' => Pages\ListPemeliharaans::route('/'),
            'create' => Pages\CreatePemeliharaan::route('/create'),
            'edit' => Pages\EditPemeliharaan::route('/{record}/edit'),
        ];
    }
}

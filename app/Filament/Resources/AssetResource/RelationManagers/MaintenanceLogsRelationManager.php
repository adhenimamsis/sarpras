<?php

namespace App\Filament\Resources\AssetResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLogsRelationManager extends RelationManager
{
    /**
     * Nama relasi di model Asset.php (hasMany).
     */
    protected static string $relationship = 'pemeliharaans';

    protected static ?string $title = 'Riwayat Pemeliharaan & Perbaikan';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Pemeliharaan Aset')
                ->description('Catat setiap tindakan pemeliharaan untuk menjaga nilai ekonomi dan fungsi aset.')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('tanggal_servis')
                            ->label('Tanggal Servis/Pemeliharaan')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\Select::make('jenis_tindakan')
                            ->label('Jenis Tindakan')
                            ->options([
                                'Perbaikan' => 'Perbaikan (Repair)',
                                'Kalibrasi' => 'Kalibrasi Eksternal',
                                'Pengecekan Rutin' => 'Pengecekan Rutin',
                                'Penggantian Suku Cadang' => 'Penggantian Suku Cadang',
                                'Rehab/Renovasi' => 'Rehab/Renovasi',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('teknisi')
                            ->label('Nama Teknisi / Vendor Pelaksana')
                            ->placeholder('Contoh: CV. Medika Jaya / Teknisi Elektromedis')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('biaya')
                            ->label('Biaya Pemeliharaan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Gunakan angka saja tanpa titik/koma.'),
                    ]),

                    Forms\Components\Textarea::make('detail_pekerjaan')
                        ->label('Deskripsi Pekerjaan')
                        ->placeholder('Tuliskan detail perbaikan atau suku cadang yang diganti...')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('file_nota')
                        ->label('Lampiran (Nota / Sertifikat / Foto)')
                        ->directory('maintenance-files')
                        ->disk('public')
                        ->visibility('public')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(5120)
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jenis_tindakan')
            ->defaultSort('tanggal_servis', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_servis')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_tindakan')
                    ->label('Tindakan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Perbaikan' => 'danger',
                        'Kalibrasi' => 'warning',
                        'Pengecekan Rutin' => 'success',
                        'Penggantian Suku Cadang' => 'info',
                        'Rehab/Renovasi' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('teknisi')
                    ->label('Pelaksana')
                    ->searchable()
                    ->description(fn ($record) => $record->detail_pekerjaan ? str($record->detail_pekerjaan)->limit(35) : null),

                Tables\Columns\TextColumn::make('biaya')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->label('Total Pengeluaran')
                            ->money('IDR', locale: 'id')
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_tindakan')
                    ->label('Jenis Tindakan'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Log')
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading('Catat Riwayat Pemeliharaan')
                    ->after(function (Model $record): void {
                        /** @var \App\Models\Asset $asset */
                        $asset = $record->asset;

                        $updateData = [
                            'kondisi' => 'Baik',
                            'tgl_maintenance_terakhir' => $record->tanggal_servis,
                        ];

                        if ($record->jenis_tindakan === 'Kalibrasi') {
                            $updateData['tgl_kalibrasi_terakhir'] = $record->tanggal_servis;
                            $updateData['tgl_kalibrasi_selanjutnya'] = Carbon::parse($record->tanggal_servis)->addYear();
                        } else {
                            $updateData['tgl_maintenance_berikutnya'] = Carbon::parse($record->tanggal_servis)->addMonths(6);
                        }

                        $asset->update($updateData);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}

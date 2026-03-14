<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationGroup = 'Sistem & Keamanan';

    protected static ?string $navigationLabel = 'Log Aktivitas';

    protected static ?string $pluralModelLabel = 'Log Aktivitas';

    /**
     * Mengatur urutan di menu navigasi (biar paling bawah di grupnya).
     */
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Jejak Audit')
                    ->description('Informasi lengkap mengenai perubahan data yang tercatat oleh sistem.')
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('asset_name')
                                ->label('Nama Aset')
                                ->readOnly(),
                            Forms\Components\TextInput::make('action')
                                ->label('Tindakan')
                                ->readOnly(),
                            Forms\Components\DateTimePicker::make('created_at')
                                ->label('Waktu Kejadian')
                                ->readOnly(),
                        ])->columns(3),

                        Forms\Components\KeyValue::make('changes')
                            ->label('Detail Perubahan Data (JSON)')
                            ->keyLabel('Atribut / Kolom')
                            ->valueLabel('Nilai Data')
                            ->columnSpanFull()
                            ->helperText('Berisi perbandingan data sebelum dan sesudah perubahan.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn (ActivityLog $record): string => $record->created_at->diffForHumans()),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Eksekutor')
                    ->searchable()
                    ->sortable()
                    ->color('primary')
                    ->weight('bold')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('action')
                    ->label('Tindakan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'TAMBAH ASET' => 'success',
                        'PERUBAHAN DATA' => 'warning',
                        'HAPUS ASET' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'TAMBAH ASET' => 'heroicon-m-plus-circle',
                        'PERUBAHAN DATA' => 'heroicon-m-pencil-square',
                        'HAPUS ASET' => 'heroicon-m-trash',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('asset_name')
                    ->label('Aset Terkait')
                    ->searchable()
                    ->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'TAMBAH ASET' => 'Penambahan',
                        'PERUBAHAN DATA' => 'Perubahan',
                        'HAPUS ASET' => 'Penghapusan',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'], fn ($q) => $q->whereDate('created_at', '>=', $data['dari_tanggal']))
                            ->when($data['sampai_tanggal'], fn ($q) => $q->whereDate('created_at', '<=', $data['sampai_tanggal']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail Log')
                    ->modalHeading('Rincian Log Aktivitas'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SECURITY & PERMISSIONS
    |--------------------------------------------------------------------------
    | Mengunci Resource agar tidak bisa dimanipulasi manual.
    */

    public static function canCreate(): bool
    {
        return false; // Dilarang input log manual
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Jejak audit tidak boleh diedit
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Jejak audit tidak boleh dihapus
    }
}

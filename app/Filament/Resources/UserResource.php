<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Manajemen Akun';

    protected static ?string $navigationGroup = 'Sistem Pengaturan';

    protected static ?int $navigationSort = 12;

    /**
     * Hak Akses: Membatasi menu ini hanya muncul untuk role Admin.
     */
    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun Pegawai')
                    ->description('Kelola data login dan hak akses sistem SimSarpras.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->placeholder('Nama lengkap sesuai SK...')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('email')
                                ->label('Alamat Email')
                                ->placeholder('email@puskesmas.go.id')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),

                            Forms\Components\Select::make('role')
                                ->label('Hak Akses (Role)')
                                ->options([
                                    'admin' => 'Administrator (Full Akses)',
                                    'teknisi' => 'Teknisi Sarpras',
                                    'staff' => 'Staff Sarpras (Operator)',
                                    'kapus' => 'Kepala Puskesmas (Viewer)',
                                ])
                                ->required()
                                ->native(false)
                                ->selectablePlaceholder(false),

                            Forms\Components\TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable() // Fitur Show/Hide Password
                                ->placeholder(fn ($record) => $record ? 'Biarkan kosong jika tidak ingin ganti password' : 'Minimal 8 karakter')
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $context): bool => $context === 'create')
                                ->rule(Password::default()),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (User $record): string => $record->email),

                Tables\Columns\TextColumn::make('role')
                    ->label('Level Akses')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'teknisi' => 'warning',
                        'staff' => 'info',
                        'kapus' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'ADMINISTRATOR',
                        'teknisi' => 'TEKNISI SARPRAS',
                        'staff' => 'OPERATOR STAFF',
                        'kapus' => 'KEPALA PUSKESMAS',
                        default => strtoupper($state),
                    }),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->default(true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Daftar Sejak')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Saring Role')
                    ->options([
                        'admin' => 'Admin',
                        'teknisi' => 'Teknisi',
                        'staff' => 'Staff',
                        'kapus' => 'Kapus',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (User $record, Tables\Actions\DeleteAction $action) {
                            // PROTEKSI: Mencegah admin menghapus dirinya sendiri
                            if ($record->id === auth()->id()) {
                                Notification::make()
                                    ->warning()
                                    ->title('Aksi Ditolak')
                                    ->body('Anda tidak diizinkan menghapus akun Anda sendiri.')
                                    ->send();

                                $action->cancel();
                            }
                        }),
                ])->icon('heroicon-m-ellipsis-vertical'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

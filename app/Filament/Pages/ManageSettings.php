<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Sistem';

    protected static ?string $title = 'Konfigurasi Puskesmas';

    protected static ?string $navigationGroup = 'Sistem Pengaturan';

    protected static ?int $navigationSort = 1000;

    protected static string $view = 'filament.pages.manage-settings';

    /**
     * Hak Akses: Hanya Admin yang boleh mengelola jantung sistem.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user() && auth()->user()->role === 'admin';
    }

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->role === 'admin';
    }

    public ?array $data = [];

    public function mount(): void
    {
        // Mengisi form dengan data dari DB. Jika kosong, default ke array kosong.
        $this->form->fill(
            Setting::pluck('value', 'key')->toArray()
        );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        // --- TAB 1: IDENTITAS ---
                        Tabs\Tab::make('Identitas & Kontak')
                            ->icon('heroicon-m-building-office-2')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('nama_puskesmas')
                                            ->label('Nama Puskesmas')
                                            ->placeholder('UPT Puskesmas Bendan')
                                            ->required(),
                                        TextInput::make('kota')
                                            ->label('Kota/Kabupaten')
                                            ->default('Pekalongan'),
                                        Textarea::make('alamat_puskesmas')
                                            ->label('Alamat Lengkap')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->required(),
                                        TextInput::make('telp_puskesmas')
                                            ->label('Nomor Telepon')
                                            ->tel(),
                                        TextInput::make('email_puskesmas')
                                            ->label('Email Resmi')
                                            ->email(),
                                    ])->columns(2),
                            ]),

                        // --- TAB 2: PEJABAT ---
                        Tabs\Tab::make('Pejabat Berwenang')
                            ->icon('heroicon-m-user-circle')
                            ->schema([
                                Section::make('Penandatangan Laporan')
                                    ->description('Nama & NIP yang muncul di Berita Acara dan Rekapitulasi')
                                    ->schema([
                                        TextInput::make('nama_kapus')
                                            ->label('Nama Kepala Puskesmas'),
                                        TextInput::make('nip_kapus')
                                            ->label('NIP Kepala Puskesmas'),
                                        TextInput::make('nama_pengurus_barang')
                                            ->label('Nama Pengurus Barang'),
                                        TextInput::make('nip_pengurus_barang')
                                            ->label('NIP Pengurus Barang'),
                                    ])->columns(2),
                            ]),

                        // --- TAB 3: INTEGRASI & NOTIFIKASI ---
                        Tabs\Tab::make('Integrasi API')
                            ->icon('heroicon-m-bolt')
                            ->schema([
                                Section::make('WhatsApp Gateway (Fonnte)')
                                    ->description('Konfigurasi pengiriman notifikasi kerusakan otomatis')
                                    ->schema([
                                        TextInput::make('wa_token')
                                            ->label('Fonnte API Token')
                                            ->password()
                                            ->helperText('Dapatkan token di dashboard fonnte.com'),
                                        TextInput::make('wa_admin_notif')
                                            ->label('Nomor WA Admin Notifikasi')
                                            ->placeholder('08xxx')
                                            ->helperText('Nomor yang akan menerima laporan kerusakan masuk'),
                                        Toggle::make('enable_wa_notif')
                                            ->label('Aktifkan Notifikasi WA')
                                            ->default(true),
                                    ])->columns(2),

                                Section::make('ASPAK')
                                    ->schema([
                                        TextInput::make('kode_registrasi_aspak')
                                            ->label('Kode Registrasi Puskesmas (ASPAK)')
                                            ->placeholder('P3375010201'),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Semua Pengaturan')
                ->icon('heroicon-m-cloud-arrow-up')
                ->color('primary')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            foreach ($data as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );

                // Menghapus cache spesifik agar perubahan instan terasa di seluruh aplikasi
                Cache::forget("setting_{$key}");
            }

            Notification::make()
                ->title('Berhasil Disimpan')
                ->success()
                ->body('Seluruh konfigurasi sistem telah diperbarui.')
                ->send();
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan pengaturan sistem.', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Gagal Menyimpan')
                ->danger()
                ->body('Terjadi kesalahan saat menyimpan pengaturan. Silakan coba lagi.')
                ->send();
        }
    }
}

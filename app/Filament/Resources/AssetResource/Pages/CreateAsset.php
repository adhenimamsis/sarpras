<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    /**
     * Memproses data secara otomatis sebelum masuk ke Database (Standardisasi KIB & MFK).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. LOGIKA STANDARISASI HURUF (UPPERCASE)
        // KIB harus seragam agar pencarian data saat pemeriksaan aset tidak terkendala typo
        $fieldsToUppercase = ['nama_alat', 'no_sertifikat', 'alamat', 'merk', 'tipe', 'konstruksi', 'no_polisi', 'no_rangka', 'no_mesin'];

        foreach ($fieldsToUppercase as $field) {
            if (! empty($data[$field])) {
                $data[$field] = strtoupper(trim($data[$field]));
            }
        }

        // 2. LOGIKA KODE BARANG & ASPAK
        // Memastikan kode unik terisi dengan benar untuk pelaporan e-Puskesmas/ASPAK
        if (isset($data['kode_alat']) && empty($data['kode_aspak'])) {
            $data['kode_aspak'] = $data['kode_alat'];
        }

        // 3. LOGIKA HARGA & SUMBER DANA
        // Jika aset berasal dari Hibah, pastikan harga perolehan minimal Rp 1 jika kosong (untuk akuntansi)
        if (! empty($data['asal_usul']) && $data['asal_usul'] === 'Hibah') {
            $data['harga_perolehan'] = $data['harga_perolehan'] ?? 1;
        }

        // 4. LOGIKA VALIDASI KATEGORI KIB (A, C, D)
        // Aset non-peralatan tidak perlu kalibrasi, namun perlu penanda kondisi fisik
        if (in_array($data['kategori_kib'], ['KIB_A', 'KIB_C', 'KIB_D'])) {
            $data['status_kalibrasi'] = false;
            $data['merk'] = $data['merk'] ?? '-';
            $data['tipe'] = $data['tipe'] ?? '-';
        }

        // 5. OTOMATISASI JADWAL PEMELIHARAAN (MFK)
        // Standar MFK: Alat Medis wajib cek rutin. Jika diinput hari ini, jadwal berikutnya +6 bulan
        if (! empty($data['tgl_maintenance_terakhir'])) {
            $data['tgl_maintenance_berikutnya'] = Carbon::parse($data['tgl_maintenance_terakhir'])
                ->addMonths(6)
                ->toDateString();
        }

        // 6. LOGIKA KALIBRASI ALKES (KIB B)
        // Jika ini Alat Kesehatan (KIB B) dan memiliki tgl kalibrasi, hitung jatuh tempo +1 tahun
        if ($data['kategori_kib'] === 'KIB_B' && ! empty($data['tgl_kalibrasi_terakhir'])) {
            $data['tgl_kalibrasi_selanjutnya'] = Carbon::parse($data['tgl_kalibrasi_terakhir'])
                ->addYear()
                ->toDateString();
        }

        return $data;
    }

    /**
     * Redirect ke halaman daftar setelah berhasil simpan.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notifikasi sukses yang dinamis dan informatif.
     */
    protected function getCreatedNotification(): ?Notification
    {
        $record = $this->getRecord();
        $namaAset = $record->nama_alat ?? 'Aset Baru';
        $kib = $record->kategori_kib;

        // Visual feedback berdasarkan kategori KIB
        $config = match ($kib) {
            'KIB_A' => ['title' => 'Tanah Terdaftar', 'icon' => 'heroicon-o-map', 'color' => 'success'],
            'KIB_B' => ['title' => 'Peralatan/Mesin Terdata', 'icon' => 'heroicon-o-wrench-screwdriver', 'color' => 'info'],
            'KIB_C' => ['title' => 'Bangunan/Gedung Baru', 'icon' => 'heroicon-o-building-office-2', 'color' => 'warning'],
            'KIB_D' => ['title' => 'Jalan/Jaringan Terinput', 'icon' => 'heroicon-o-share', 'color' => 'primary'],
            default => ['title' => 'Aset Berhasil Disimpan', 'icon' => 'heroicon-o-check-circle', 'color' => 'success'],
        };

        return Notification::make()
            ->success()
            ->title($config['title'])
            ->body("Aset **{$namaAset}** (Reg: **".($record->no_register ?? '-').'**) berhasil ditambahkan ke inventaris Puskesmas.')
            ->icon($config['icon'])
            ->color($config['color'])
            ->duration(6000);
    }
}

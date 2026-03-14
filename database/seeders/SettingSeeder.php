<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // --- GROUP: IDENTITAS ---
            ['key' => 'nama_puskesmas', 'value' => 'UPT Puskesmas Bendan', 'group' => 'identitas', 'type' => 'text', 'label' => 'Nama Puskesmas'],
            ['key' => 'alamat_puskesmas', 'value' => 'Jl. Slamet No. 2 Pekalongan', 'group' => 'identitas', 'type' => 'textarea', 'label' => 'Alamat Lengkap'],
            ['key' => 'kota', 'value' => 'Pekalongan', 'group' => 'identitas', 'type' => 'text', 'label' => 'Kota/Kabupaten'],
            ['key' => 'telp_puskesmas', 'value' => '(0285) 421442', 'group' => 'identitas', 'type' => 'text', 'label' => 'Nomor Telepon'],
            ['key' => 'email_puskesmas', 'value' => 'uptpuskesmasbendan@yahoo.co.id', 'group' => 'identitas', 'type' => 'text', 'label' => 'Email Resmi'],

            // --- GROUP: PEJABAT (Untuk Tanda Tangan Laporan) ---
            ['key' => 'nama_kapus', 'value' => 'Nama Kepala Puskesmas, SKM', 'group' => 'pejabat', 'type' => 'text', 'label' => 'Nama Kepala Puskesmas'],
            ['key' => 'nip_kapus', 'value' => '198001012005011001', 'group' => 'pejabat', 'type' => 'text', 'label' => 'NIP Kepala Puskesmas'],
            ['key' => 'nama_pengurus_barang', 'value' => 'Nama Pengurus Barang', 'group' => 'pejabat', 'type' => 'text', 'label' => 'Nama Pengurus Barang'],
            ['key' => 'nip_pengurus_barang', 'value' => '199002022015032002', 'group' => 'pejabat', 'type' => 'text', 'label' => 'NIP Pengurus Barang'],

            // --- GROUP: API & NOTIFIKASI (WA Gateway Fonnte) ---
            ['key' => 'wa_token', 'value' => null, 'group' => 'api', 'type' => 'text', 'label' => 'Fonnte API Token'],
            ['key' => 'wa_admin_notif', 'value' => '08123456789', 'group' => 'notifikasi', 'type' => 'text', 'label' => 'Nomor WA Notifikasi Admin'],
            ['key' => 'enable_wa_notif', 'value' => '1', 'group' => 'notifikasi', 'type' => 'toggle', 'label' => 'Aktifkan Notifikasi WhatsApp'],

            // --- GROUP: INTEGRASI ---
            ['key' => 'kode_registrasi_aspak', 'value' => 'P3375010201', 'group' => 'integrasi', 'type' => 'text', 'label' => 'Kode ASPAK Puskesmas'],
        ];

        foreach ($data as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'],
                    'group' => $item['group'],
                    'type' => $item['type'],
                    'label' => $item['label'],
                ]
            );
        }
    }
}

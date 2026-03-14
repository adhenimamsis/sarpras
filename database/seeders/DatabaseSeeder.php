<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. SEEDER IDENTITAS & SETTING GLOBAL
        // Agar sistem tidak kosong saat pertama kali dibuka
        $this->seedGlobalSettings();

        // 2. SEEDER DATA MASTER RUANGAN
        // Pastikan file RoomSeeder.php sudah bos buat
        if (class_exists(RoomSeeder::class)) {
            $this->call([
                RoomSeeder::class,
            ]);
        }

        // 3. BUAT USER MULTI-ROLE (Admin, Teknisi, Kapus)
        // Menggunakan updateOrCreate agar aman dijalankan berkali-kali
        $this->seedUsers();

        // 4. SEEDER OKSIGEN (Optional: Untuk testing awal)
        // $this->call([StokOksigenSeeder::class]);
    }

    /**
     * Logic pembuatan User awal.
     */
    private function seedUsers(): void
    {
        $seedPassword = (string) env('SEED_USER_PASSWORD', '');

        if ($seedPassword === '' && app()->environment('production')) {
            throw new RuntimeException('SEED_USER_PASSWORD wajib diisi saat seeding di production.');
        }

        if ($seedPassword === '') {
            // Default dev password (bukan "password") agar lebih aman untuk non-production.
            $seedPassword = 'ChangeMe!123';
        }

        $users = [
            [
                'name' => 'Admin SimSarpras',
                'email' => 'admin@bendan.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Teknisi Sarpras',
                'email' => 'teknisi@bendan.com',
                'role' => 'teknisi',
            ],
            [
                'name' => 'Kepala Puskesmas',
                'email' => 'kapus@bendan.com',
                'role' => 'kapus',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($seedPassword),
                    'role' => $userData['role'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }

    /**
     * Logic pengisian Setting awal.
     */
    private function seedGlobalSettings(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'SimSarpras Bendan', 'group' => 'identitas', 'type' => 'text'],
            ['key' => 'nama_puskesmas', 'value' => 'UPT Puskesmas Bendan', 'group' => 'identitas', 'type' => 'text'],
            ['key' => 'alamat_puskesmas', 'value' => 'Jl. Pemuda No. 1, Pekalongan', 'group' => 'identitas', 'type' => 'textarea'],
            ['key' => 'wa_token', 'value' => 'GANTI_DENGAN_TOKEN_FONNTE', 'group' => 'api', 'type' => 'text'],
            ['key' => 'wa_admin_notif', 'value' => '08123456789', 'group' => 'notifikasi', 'type' => 'text'],
        ];

        foreach ($settings as $set) {
            Setting::updateOrCreate(['key' => $set['key']], $set);
        }
    }
}

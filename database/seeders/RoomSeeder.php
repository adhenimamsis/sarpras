<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruangans = [
            // --- GEDUNG UTAMA (Lantai 1) ---
            ['kode_ruangan' => 'A', 'nama_ruangan' => 'Ruang Laboratorium', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'B', 'nama_ruangan' => 'Pelayanan Kesehatan Gigi & Mulut 1', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'C', 'nama_ruangan' => 'Pelayanan Kesehatan Gigi & Mulut 2', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'D', 'nama_ruangan' => 'Ruang Farmasi', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'E', 'nama_ruangan' => 'Ruang Penanganan Keluhan', 'kategori' => 'Umum', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'F', 'nama_ruangan' => 'Ruang Konseling Apoteker', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'G', 'nama_ruangan' => 'Ruang Pemeriksaan Lansia', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'H', 'nama_ruangan' => 'Ruang Tindakan Mata', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'I', 'nama_ruangan' => 'Ruang Konsultasi', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'J', 'nama_ruangan' => 'Ruang Tindakan', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'N', 'nama_ruangan' => 'Ruang Pemeriksaan Umum 1', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'O', 'nama_ruangan' => 'Loket Pendaftaran', 'kategori' => 'Umum', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'S', 'nama_ruangan' => 'Ruang Tunggu Pasien', 'kategori' => 'Umum', 'gedung' => 'Gedung Utama'],

            // --- GEDUNG RAWAT INAP & PONED ---
            ['kode_ruangan' => 'T', 'nama_ruangan' => 'Ruang Pelayanan Gawat Darurat', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Rawat Inap'],
            ['kode_ruangan' => 'U', 'nama_ruangan' => 'Ruang PONED', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Rawat Inap'],
            ['kode_ruangan' => 'V', 'nama_ruangan' => 'Ruang Persalinan', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Rawat Inap'],
            ['kode_ruangan' => 'W', 'nama_ruangan' => 'Ruang Pasca Persalinan', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Rawat Inap'],
            ['kode_ruangan' => 'Ag', 'nama_ruangan' => 'Ruang Mawar 1 (Anak)', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Rawat Inap'],
            ['kode_ruangan' => 'Ah', 'nama_ruangan' => 'Ruang Mawar 2 (Anak)', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Rawat Inap'],
            ['kode_ruangan' => 'Ai', 'nama_ruangan' => 'Ruang Flamboyan (R. Isolasi)', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Rawat Inap'],
            ['kode_ruangan' => 'Aj', 'nama_ruangan' => 'Ruang Kenanga (Dewasa Laki-Laki)', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Rawat Inap'],
            ['kode_ruangan' => 'Ak', 'nama_ruangan' => 'Ruang Dahlia (Dewasa Perempuan)', 'kategori' => 'Pelayanan', 'gedung' => 'Gedung Rawat Inap'],

            // --- GEDUNG JLAMPRANG & MANAJEMEN (Lantai 2) ---
            ['kode_ruangan' => 'BA', 'nama_ruangan' => 'Ruang Tata Usaha', 'kategori' => 'Manajemen', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'BC', 'nama_ruangan' => 'Ruang Keuangan', 'kategori' => 'Manajemen', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'Bg', 'nama_ruangan' => 'Ruang Kepala Puskesmas', 'kategori' => 'Manajemen', 'gedung' => 'Gedung Utama'],
            ['kode_ruangan' => 'X6', 'nama_ruangan' => 'Aula Gedung Jlamprang', 'kategori' => 'Umum', 'gedung' => 'Gedung Jlamprang'],

            // --- INFRASTRUKTUR & PENUNJANG ---
            ['kode_ruangan' => 'X1', 'nama_ruangan' => 'IPAL', 'kategori' => 'Infrastruktur', 'gedung' => 'Luar Gedung'],
            ['kode_ruangan' => 'X3', 'nama_ruangan' => 'Ruang Genset', 'kategori' => 'Infrastruktur', 'gedung' => 'Luar Gedung'],
            ['kode_ruangan' => 'AA', 'nama_ruangan' => 'Parkir Ambulance', 'kategori' => 'Infrastruktur', 'gedung' => 'Luar Gedung'],
            ['kode_ruangan' => 'X4', 'nama_ruangan' => 'Ruang Cuci Linen / Laundry', 'kategori' => 'Penunjang', 'gedung' => 'Gedung Rawat Inap'],
        ];

        foreach ($ruangans as $ruangan) {
            Ruangan::updateOrCreate(
                ['kode_ruangan' => $ruangan['kode_ruangan']],
                [
                    'nama_ruangan' => $ruangan['nama_ruangan'],
                    'kategori' => $ruangan['kategori'],
                    'gedung' => $ruangan['gedung'],
                    'lantai' => in_array($ruangan['kode_ruangan'], ['BA', 'BC', 'Bg']) ? 'Lantai 2' : 'Lantai 1',
                ]
            );
        }
    }
}

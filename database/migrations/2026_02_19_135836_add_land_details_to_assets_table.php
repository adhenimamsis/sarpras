<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan detail legalitas KIB A/C dan jadwal monitoring MFK pada tabel assets.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // --- 1. KOLOM LEGALITAS & LOKASI ---
            // Kita gunakan 'kondisi' sebagai jangkar utama karena kolom ini pasti ada
            if (! Schema::hasColumn('assets', 'alamat')) {
                $table->text('alamat')->nullable()->after('kondisi');
                $table->string('no_sertifikat')->nullable()->after('alamat');
                $table->decimal('luas_meter', 15, 2)->nullable()->after('no_sertifikat');
                $table->string('status_kepemilikan')->nullable()->after('luas_meter');
            }

            // --- 2. KOLOM MONITORING MFK & GARANSI ---
            // Kita cek satu per satu untuk menghindari error dependensi urutan (after)
            if (! Schema::hasColumn('assets', 'tgl_maintenance_terakhir')) {
                $table->date('tgl_maintenance_terakhir')->nullable()->after('status_kepemilikan');
            }

            if (! Schema::hasColumn('assets', 'tgl_maintenance_berikutnya')) {
                $table->date('tgl_maintenance_berikutnya')->nullable()->after('tgl_maintenance_terakhir');
            }

            if (! Schema::hasColumn('assets', 'tgl_garansi_habis')) {
                $table->date('tgl_garansi_habis')->nullable()->after('tgl_maintenance_berikutnya');
            }

            // --- 3. KOLOM PENDUKUNG ADMINISTRASI ---
            // Perbaikan Error: Jika tgl_garansi_habis gagal ditemukan, kolom ini akan otomatis ditaruh di akhir
            if (! Schema::hasColumn('assets', 'no_pbg_slf')) {
                $anchor = Schema::hasColumn('assets', 'tgl_garansi_habis') ? 'tgl_garansi_habis' : 'kondisi';

                $table->string('no_pbg_slf')->nullable()->after($anchor)
                    ->comment('Nomor Persetujuan Bangunan Gedung / Sertifikat Laik Fungsi');

                $table->string('no_ijin_edar')->nullable()->after('no_pbg_slf')
                    ->comment('Khusus Alkes untuk sinkronisasi ASPAK');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $columns = [
                'alamat', 'no_sertifikat', 'luas_meter', 'status_kepemilikan',
                'tgl_maintenance_terakhir', 'tgl_maintenance_berikutnya', 'tgl_garansi_habis',
                'no_pbg_slf', 'no_ijin_edar',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('assets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

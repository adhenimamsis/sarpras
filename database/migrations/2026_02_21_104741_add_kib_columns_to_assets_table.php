<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom standar KIB dan monitoring pemeliharaan.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // --- 1. PENGGOLONGAN KIB ---
            if (! Schema::hasColumn('assets', 'kategori_kib')) {
                $table->string('kategori_kib')->nullable()->index()->after('id')
                    ->comment('KIB A (Tanah), B (Peralatan), C (Bangunan), D (Jalan), E (Aset Lain)');
            }
            if (! Schema::hasColumn('assets', 'no_register')) {
                $table->string('no_register')->nullable()->index()->after('kategori_kib');
            }

            // --- 2. DETAIL LEGALITAS & FISIK (KIB A, C, D) ---
            if (! Schema::hasColumn('assets', 'no_sertifikat')) {
                $table->string('no_sertifikat')->nullable()->after('no_register');
            }
            if (! Schema::hasColumn('assets', 'luas_meter')) {
                $table->decimal('luas_meter', 15, 2)->nullable()->after('no_sertifikat');
            }
            if (! Schema::hasColumn('assets', 'alamat')) {
                $table->string('alamat')->nullable()->after('luas_meter');
            }
            if (! Schema::hasColumn('assets', 'konstruksi')) {
                $table->string('konstruksi')->nullable()->after('alamat')
                    ->comment('Beton, Kayu, Aspal, dsb.');
            }

            // --- 3. PENGADAAN & MONITORING (MFK) ---
            if (! Schema::hasColumn('assets', 'asal_usul')) {
                $table->string('asal_usul')->nullable()->after('konstruksi')
                    ->comment('APBD, DAK, Hibah, dsb.');
            }
            if (! Schema::hasColumn('assets', 'tgl_maintenance_terakhir')) {
                $table->date('tgl_maintenance_terakhir')->nullable()->after('asal_usul');
            }
            if (! Schema::hasColumn('assets', 'tgl_maintenance_berikutnya')) {
                $table->date('tgl_maintenance_berikutnya')->nullable()->after('tgl_maintenance_terakhir');
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
                'kategori_kib', 'no_register', 'no_sertifikat', 'luas_meter',
                'alamat', 'asal_usul', 'konstruksi',
                'tgl_maintenance_terakhir', 'tgl_maintenance_berikutnya',
            ];

            // Menghapus hanya kolom yang memang ada di tabel
            foreach ($columns as $column) {
                if (Schema::hasColumn('assets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

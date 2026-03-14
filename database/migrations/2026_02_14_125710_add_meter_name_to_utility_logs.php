<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan spesifikasi titik ukur pada pencatatan utilitas.
     */
    public function up(): void
    {
        Schema::table('utility_logs', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada agar tidak error "Duplicate column"
            if (! Schema::hasColumn('utility_logs', 'nama_meteran')) {
                $table->string('nama_meteran')
                    ->nullable()
                    ->after('jenis')
                    ->index() // Ditambah index agar pencarian per gedung lebih cepat
                    ->comment('Contoh: Meteran Utama, Meteran Gedung B, Panel IGD');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utility_logs', function (Blueprint $table) {
            if (Schema::hasColumn('utility_logs', 'nama_meteran')) {
                $table->dropColumn('nama_meteran');
            }
        });
    }
};

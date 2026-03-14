<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan informasi legalitas tanah dan bangunan pada tabel ruangans.
     */
    public function up(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            // Kita bungkus dengan pengecekan agar migrasi bersifat idempotent (aman dijalankan ulang)
            if (! Schema::hasColumn('ruangans', 'status_tanah')) {
                $table->string('status_tanah')->nullable()->after('keterangan');
                $table->text('alamat_lokasi')->nullable()->after('status_tanah');
                $table->string('no_sertifikat')->nullable()->after('alamat_lokasi');
                $table->string('penggunaan')->nullable()->after('no_sertifikat');
                $table->string('asal_usul')->nullable()->after('penggunaan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            // Menghapus kolom jika ingin membatalkan migrasi
            $table->dropColumn([
                'status_tanah',
                'alamat_lokasi',
                'no_sertifikat',
                'penggunaan',
                'asal_usul',
            ]);
        });
    }
};

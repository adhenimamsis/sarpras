<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hardening: jangan hapus tabel existing di environment produksi.
        if (Schema::hasTable('utility_logs')) {
            return;
        }

        Schema::create('utility_logs', function (Blueprint $table) {
            $table->id();

            // --- IDENTITAS WAKTU & LOKASI ---
            $table->date('tgl_catat')->index(); // Tanggal pengambilan data
            $table->string('jenis')->index();   // listrik, air, solar, gas_medik, ipal
            $table->string('nama_meteran')->nullable()->index(); // Lokasi titik ukur (Gedung A, B, dll)

            // --- DATA PENGUKURAN ---
            // Menggunakan decimal agar presisi untuk angka meteran yang besar
            $table->decimal('angka_meteran', 15, 2);

            // --- AKUNTABILITAS ---
            $table->string('petugas')->nullable();
            $table->text('catatan')->nullable();   // Contoh: "Kenaikan drastis karena pompa bocor"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_logs');
    }
};

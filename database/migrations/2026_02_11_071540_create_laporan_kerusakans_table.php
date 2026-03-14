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
        // Hardening: jangan drop tabel eksisting agar data produksi aman.
        if (Schema::hasTable('laporan_kerusakans')) {
            return;
        }

        Schema::create('laporan_kerusakans', function (Blueprint $table) {
            $table->id();

            // --- RELASI ---
            // Menghubungkan ke tabel assets (Master KIB)
            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            // --- DATA PELAPORAN ---
            $table->string('pelapor')->index();
            $table->text('deskripsi_kerusakan');
            $table->date('tgl_lapor')->index();

            // --- WORKFLOW STATUS ---
            // Menggunakan string agar lebih fleksibel jika ada tambahan status di masa depan
            $table->string('status')->default('Lapor')->index(); // Lapor, Proses, Selesai

            // --- DATA PERBAIKAN ---
            $table->date('tgl_selesai')->nullable();
            $table->text('tindakan_perbaikan')->nullable();
            $table->string('teknisi_penanggung_jawab')->nullable();

            // --- BUKTI VISUAL ---
            $table->string('foto_kerusakan')->nullable(); // Foto saat barang rusak
            $table->string('foto_perbaikan')->nullable(); // Foto setelah diperbaiki (bukti selesai)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kerusakans');
    }
};

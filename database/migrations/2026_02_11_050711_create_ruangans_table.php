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
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();

            // --- IDENTITAS DASAR ---
            // Kode unik (Contoh: R-001, IGD-01)
            $table->string('kode_ruangan')->unique()->index();
            $table->string('nama_ruangan');

            // Pengelompokan Lokasi
            $table->string('gedung')->nullable(); // Gedung Utama, PONED, dll
            $table->string('lantai')->default('Lantai 1');
            $table->string('kategori')->nullable(); // Pelayanan, Penunjang, Umum

            // --- DATA LEGALITAS & ASPAK ---
            // Berguna untuk sinkronisasi aset KIB A (Tanah) dan C (Bangunan)
            $table->string('status_tanah')->nullable(); // Hak Pakai / Milik Pemda
            $table->string('no_sertifikat')->nullable();
            $table->string('asal_usul')->nullable();
            $table->string('penggunaan')->nullable();
            $table->text('alamat_lokasi')->nullable();

            // --- VISUAL & MAPPING ---
            $table->string('foto_denah')->nullable(); // Path file denah/foto ruangan
            // Koordinat untuk fitur Interactive Floor Plan nantinya
            $table->decimal('koordinat_x', 10, 2)->nullable();
            $table->decimal('koordinat_y', 10, 2)->nullable();

            // --- INFORMASI TAMBAHAN ---
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};

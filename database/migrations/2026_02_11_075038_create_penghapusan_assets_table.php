<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel penghapusan_assets.
     */
    public function up(): void
    {
        // Hardening: jangan drop tabel eksisting agar data produksi aman.
        if (Schema::hasTable('penghapusan_assets')) {
            return;
        }

        Schema::create('penghapusan_assets', function (Blueprint $table) {
            $table->id();

            // --- RELASI ---
            // Menghubungkan ke master aset.
            // Cascade delete memastikan jika record aset dihapus, history ini ikut bersih.
            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            // --- DATA ADMINISTRASI ---
            $table->date('tgl_penghapusan')->index();
            $table->string('no_sk')->nullable()->index(); // No SK Penghapusan / No Berita Acara
            $table->string('alasan'); // Rusak Berat, Hilang, Kadaluarsa, Hibah Keluar
            $table->string('metode'); // Pemusnahan, Lelang, Hibah, Tukar Menukar

            // --- DATA FINANSIAL (Opsional) ---
            // Berguna jika aset dilelang atau dijual sebagai rongsok/scrap
            $table->decimal('nilai_jual', 15, 2)->default(0);

            // --- DOKUMENTASI & BUKTI ---
            $table->text('keterangan')->nullable();
            $table->string('foto_bukti')->nullable();    // Foto fisik barang saat dimusnahkan
            $table->string('file_ba_penghapusan')->nullable(); // Scan PDF Berita Acara resmi

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('penghapusan_assets');
    }
};

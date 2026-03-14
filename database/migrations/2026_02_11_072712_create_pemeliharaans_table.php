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
        if (Schema::hasTable('pemeliharaans')) {
            return;
        }

        Schema::create('pemeliharaans', function (Blueprint $table) {
            $table->id();

            // --- RELASI ---
            // Menghubungkan ke tabel assets (Master Aset)
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            // Menghubungkan ke tabel users (Petugas yang input di sistem)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // --- DATA KEGIATAN ---
            $table->string('kegiatan'); // Misal: Cek suhu, ganti baterai, kuras tangki
            $table->date('tgl_pemeliharaan')->index(); // Menggabungkan tgl_jadwal & realisasi agar lebih simpel

            // --- PELAKSANAAN & STATUS ---
            $table->string('petugas')->nullable(); // Nama teknisi lapangan (bisa vendor luar)
            $table->string('status')->default('Terjadwal')->index(); // Terjadwal, Selesai

            // --- FINANSIAL & CATATAN ---
            // Menggunakan decimal(15,2) agar sinkron dengan harga perolehan di tabel assets
            $table->decimal('biaya', 15, 2)->default(0);
            $table->text('keterangan')->nullable();

            // Lampiran Foto Kegiatan
            $table->string('lampiran_foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeliharaans');
    }
};

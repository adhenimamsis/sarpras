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
        if (Schema::hasTable('mfk_pemeliharaans')) {
            return;
        }

        Schema::create('mfk_pemeliharaans', function (Blueprint $table) {
            $table->id();

            // --- RELASI ---
            // Menghubungkan log pemeliharaan dengan ID Aset (Master KIB)
            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            // --- PENJADWALAN MFK ---
            $table->date('tgl_pemeliharaan')->index();
            $table->date('tgl_berikutnya')->nullable()->index();

            // --- DETAIL TEKNIS ---
            $table->text('uraian_kegiatan');
            $table->string('hasil_perbaikan')->index(); // Berfungsi, Rusak, Perlu Kalibrasi
            $table->text('keterangan_tambahan')->nullable();

            // --- AKUNTABILITAS & BIAYA ---
            // Menggunakan decimal agar sinkron dengan perhitungan nilai buku aset
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('petugas'); // Teknisi internal atau Nama Vendor

            // --- DOKUMENTASI & BUKTI FISIK ---
            $table->string('foto_nota')->nullable();
            $table->string('file_ba_perbaikan')->nullable(); // Upload PDF Berita Acara jika ada

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfk_pemeliharaans');
    }
};

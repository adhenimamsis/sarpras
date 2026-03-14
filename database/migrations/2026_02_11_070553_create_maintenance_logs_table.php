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
        if (Schema::hasTable('maintenance_logs')) {
            return;
        }

        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();

            // --- RELASI ---
            // Menghubungkan log dengan Master Aset (KIB)
            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            // --- DETAIL WAKTU ---
            $table->date('tanggal_servis')->index();
            $table->date('tgl_selesai_garansi')->nullable(); // Masa berlaku garansi servis

            // --- DETAIL TINDAKAN ---
            $table->string('jenis_tindakan'); // Rutin, Perbaikan, Kalibrasi, Penggantian Part
            $table->string('teknisi');        // Nama petugas/teknisi
            $table->string('vendor')->nullable(); // Nama perusahaan/toko jika servis luar
            $table->text('detail_pekerjaan');

            // --- FINANSIAL ---
            // Menggunakan decimal(15,2) agar sinkron dengan sistem akuntansi aset
            $table->decimal('biaya', 15, 2)->default(0);

            // --- DOKUMENTASI ---
            $table->string('file_nota')->nullable(); // Path foto nota/invoice
            $table->string('file_ba_servis')->nullable(); // Berita Acara Servis (PDF)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};

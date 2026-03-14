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
        if (Schema::hasTable('kalibrasis')) {
            return;
        }

        Schema::create('kalibrasis', function (Blueprint $table) {
            $table->id();

            // --- RELASI ---
            // Menghubungkan log kalibrasi dengan Master Aset Alkes
            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            // --- PERIODE KALIBRASI ---
            $table->date('tgl_kalibrasi')->index();
            $table->date('tgl_kadaluarsa')->index(); // Tanggal wajib re-kalibrasi

            // --- DATA TEKNIS & SERTIFIKASI ---
            $table->string('pelaksana'); // Contoh: BPFK atau Vendor Swasta Terakreditasi
            $table->string('no_sertifikat')->nullable()->index();
            $table->string('hasil')->default('Layak')->index(); // Layak, Tidak Layak, Layak dengan Catatan

            // --- AKUNTABILITAS & DOKUMEN ---
            // Menggunakan decimal agar sinkron dengan master aset
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('file_sertifikat')->nullable(); // Path PDF sertifikat hasil uji

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasis');
    }
};

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
        if (Schema::hasTable('stok_oksigens')) {
            return;
        }

        Schema::create('stok_oksigens', function (Blueprint $table) {
            $table->id();

            // --- IDENTITAS LOKASI & BARANG ---
            // Contoh: Gudang Farmasi, IGD, Ruang Rawat Inap
            $table->string('lokasi')->index();

            // Ukuran tabung (Dibuat string agar fleksibel jika ada ukuran baru seperti 2m3)
            $table->string('ukuran')->index();

            // --- MUTASI STOK ---
            $table->integer('jumlah_masuk')->default(0);
            $table->integer('jumlah_keluar')->default(0);

            // Saldo berjalan (Running Balance)
            $table->integer('stok_akhir')->index();

            // --- AKUNTABILITAS ---
            $table->string('petugas'); // Nama petugas yang melakukan mutasi
            $table->text('keterangan')->nullable(); // Contoh: Pengisian ulang dari vendor A

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_oksigens');
    }
};

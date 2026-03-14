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
        if (Schema::hasTable('monitoring_mfks')) {
            return;
        }

        Schema::create('monitoring_mfks', function (Blueprint $table) {
            $table->id();

            // --- IDENTITAS PEMERIKSAAN ---
            $table->string('jenis_utilitas')->index(); // Listrik, Air, Gas Medik, APAR, IPAL, Gedung
            $table->date('tgl_cek')->index();
            $table->time('waktu_cek');
            $table->string('status')->default('Normal')->index(); // Normal, Gangguan, Perbaikan
            $table->string('petugas')->index();

            // --- DATA DETAIL (STRUKTUR JSON) ---
            // Menyimpan hasil checklist dari Filament CheckboxList/Repeater
            $table->json('check_listrik')->nullable();
            $table->json('detail_apar')->nullable();
            $table->json('kondisi_air')->nullable();
            $table->json('check_bangunan')->nullable();
            $table->json('detail_ipal')->nullable();

            // --- PARAMETER TEKNIS SPESIFIK ---
            // Gas Medik
            $table->integer('tekanan_oksigen')->nullable();
            $table->string('stok_cadangan')->nullable();

            // IPAL / Limbah
            $table->decimal('debit_air_limbah', 10, 2)->nullable();
            $table->string('kondisi_bak')->nullable();

            // --- NARASI & BUKTI FISIK ---
            $table->text('parameter_cek')->nullable(); // Catatan teknis detail
            $table->text('foto_bukti')->nullable();    // Path file foto (bisa array/multiple)
            $table->text('keterangan')->nullable();    // Rencana Tindak Lanjut (RTL)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_mfks');
    }
};

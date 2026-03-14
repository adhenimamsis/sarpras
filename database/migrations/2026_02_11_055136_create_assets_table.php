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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            // --- PENGGOLONGAN KIB (Kartu Inventaris Barang) ---
            $table->string('kategori_kib')->index(); // KIB_A, KIB_B, KIB_C, KIB_D, KIB_E, KIB_F
            $table->string('no_register')->nullable()->index(); // Nomor urut pendaftaran barang

            // --- IDENTITAS UMUM ---
            $table->string('nama_alat');
            $table->string('kode_aspak')->nullable()->index();
            $table->decimal('harga_perolehan', 20, 2)->default(0); // Menggunakan decimal untuk akurasi nilai buku
            $table->year('tahun_perolehan')->nullable();

            // Menggunakan kode singkat agar sinkron dengan logic Model/Resource
            // B=Baik, RR=Rusak Ringan, RB=Rusak Berat, Dihapuskan
            $table->string('kondisi')->default('B')->index();

            // --- SPESIFIKASI TEKNIS (KIB B) ---
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->string('no_seri')->nullable();
            $table->string('asal_usul')->nullable(); // Pembelian, Hibah, APBD, dsb.

            // --- DATA FISIK & LEGALITAS (KIB A, C, D) ---
            $table->text('alamat')->nullable(); // Lokasi tanah/gedung
            $table->string('no_sertifikat')->nullable(); // No Sertifikat Tanah / IMB / No Dokumen
            $table->decimal('luas_meter', 15, 2)->nullable(); // Luas m2 atau panjang m
            $table->string('konstruksi')->nullable(); // Beton, Aspal, Kayu, dsb.

            // --- RELASI & POSISI ---
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->onDelete('set null');
            $table->string('lokasi_detail')->nullable(); // Posisi spesifik di dalam ruangan

            // --- DOKUMEN & MONITORING MFK ---
            $table->text('foto')->nullable(); // Cast array di model (simpan path foto)
            $table->text('sertifikat')->nullable(); // Cast array di model (scan dokumen)
            $table->boolean('status_kalibrasi')->default(false);

            $table->date('tgl_kalibrasi_terakhir')->nullable();
            $table->date('tgl_kalibrasi_selanjutnya')->nullable();

            $table->date('tgl_maintenance_terakhir')->nullable();
            $table->date('tgl_maintenance_berikutnya')->nullable();

            $table->date('tgl_kadaluarsa')->nullable(); // Expired Alat/Reagen/APAR

            // --- KATEGORI SISTEM (ASPAK) ---
            $table->string('kategori_utilitas')->nullable(); // Alkes, Non-Alkes, Utilitas, IPAL, dll.

            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

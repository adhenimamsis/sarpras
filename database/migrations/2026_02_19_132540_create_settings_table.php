<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel pusat konfigurasi sistem SimSarpras.
     */
    public function up(): void
    {
        // Hardening: jangan drop tabel eksisting agar data produksi aman.
        if (Schema::hasTable('settings')) {
            return;
        }

        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // 'key' unik sebagai pengenal (contoh: 'app_logo', 'wa_token')
            $table->string('key')->unique()->index();

            // 'value' untuk isi konfigurasinya
            $table->text('value')->nullable();

            // 'group' untuk pengelompokan di UI (identitas, api, notifikasi)
            $table->string('group')->default('umum')->index();

            // 'type' untuk menentukan jenis inputan di Filament (text, textarea, image, toggle)
            $table->string('type')->default('text');

            // Metadata tambahan (opsional: untuk label atau instruksi di UI)
            $table->string('label')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

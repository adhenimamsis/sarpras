<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini digunakan oleh Laravel & Filament untuk menyimpan notifikasi sistem.
     */
    public function up(): void
    {
        // Hardening: jangan hapus tabel existing di environment produksi.
        if (Schema::hasTable('notifications')) {
            return;
        }

        Schema::create('notifications', function (Blueprint $table) {
            // Menggunakan UUID sebagai primary key (Standar Laravel Notification)
            $table->uuid('id')->primary();

            // Tipe Notifikasi (Contoh: App\Notifications\LaporanKerusakanBaru)
            $table->string('type');

            // Relasi Polymorphic (Bisa dikirim ke User, Admin, atau Kapus)
            $table->morphs('notifiable');

            // Data JSON (Berisi isi pesan, link, dan info aset terkait)
            $table->text('data');

            // Status dibaca
            $table->timestamp('read_at')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

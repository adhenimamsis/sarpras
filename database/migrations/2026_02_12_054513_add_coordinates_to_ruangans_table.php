<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan koordinat pemetaan untuk fitur denah interaktif.
     */
    public function up(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            // Menggunakan decimal agar posisi pin tetap presisi di berbagai ukuran layar
            if (! Schema::hasColumn('ruangans', 'koordinat_x')) {
                $table->decimal('koordinat_x', 10, 2)->nullable()->after('foto_denah');
            }

            if (! Schema::hasColumn('ruangans', 'koordinat_y')) {
                $table->decimal('koordinat_y', 10, 2)->nullable()->after('koordinat_x');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            if (Schema::hasColumn('ruangans', 'koordinat_x')) {
                $table->dropColumn(['koordinat_x', 'koordinat_y']);
            }
        });
    }
};

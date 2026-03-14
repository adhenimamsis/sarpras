<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan detail gedung, denah, dan legalitas pada tabel ruangans.
     */
    public function up(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            // --- KOLOM LOKASI & VISUAL ---
            if (! Schema::hasColumn('ruangans', 'gedung')) {
                $table->string('gedung')->nullable()->after('nama_ruangan')->index();
            }

            if (! Schema::hasColumn('ruangans', 'foto_denah')) {
                $table->string('foto_denah')->nullable()->after('keterangan');
            }

            // Koordinat untuk mapping aset di dalam denah
            if (! Schema::hasColumn('ruangans', 'koordinat_x')) {
                $table->decimal('koordinat_x', 10, 2)->nullable()->after('foto_denah');
                $table->decimal('koordinat_y', 10, 2)->nullable()->after('koordinat_x');
            }

            // --- KOLOM LEGALITAS & PENGGUNAAN (Sinkronisasi KIB) ---
            if (! Schema::hasColumn('ruangans', 'status_tanah')) {
                $table->string('status_tanah')->nullable()->after('koordinat_y');
                $table->string('no_sertifikat')->nullable()->after('status_tanah');
                $table->string('penggunaan')->nullable()->after('no_sertifikat');
                $table->string('asal_usul')->nullable()->after('penggunaan');
                $table->text('alamat_lokasi')->nullable()->after('asal_usul');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $columns = [
                'gedung', 'foto_denah', 'koordinat_x', 'koordinat_y',
                'status_tanah', 'no_sertifikat', 'penggunaan', 'asal_usul', 'alamat_lokasi',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('ruangans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

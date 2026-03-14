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
        if (! Schema::hasTable('mfk_pemeliharaans')) {
            Schema::create('mfk_pemeliharaans', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('asset_id')->nullable()->index();
                $table->date('tgl_pemeliharaan')->nullable()->index();
                $table->date('tgl_berikutnya')->nullable()->index();
                $table->text('uraian_kegiatan')->nullable();
                $table->string('hasil_perbaikan')->nullable()->index();
                $table->text('keterangan_tambahan')->nullable();
                $table->decimal('biaya', 15, 2)->default(0);
                $table->string('petugas')->nullable();
                $table->string('foto_nota')->nullable();
                $table->string('file_ba_perbaikan')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('mfk_pemeliharaans', function (Blueprint $table): void {
            if (! Schema::hasColumn('mfk_pemeliharaans', 'asset_id')) {
                $table->foreignId('asset_id')->nullable()->index()->after('id');
            }

            if (! Schema::hasColumn('mfk_pemeliharaans', 'tgl_pemeliharaan')) {
                $table->date('tgl_pemeliharaan')->nullable()->index()->after('asset_id');
            }

            if (! Schema::hasColumn('mfk_pemeliharaans', 'tgl_berikutnya')) {
                $table->date('tgl_berikutnya')->nullable()->index()->after('tgl_pemeliharaan');
            }

            if (! Schema::hasColumn('mfk_pemeliharaans', 'uraian_kegiatan')) {
                $table->text('uraian_kegiatan')->nullable()->after('tgl_berikutnya');
            }

            if (! Schema::hasColumn('mfk_pemeliharaans', 'hasil_perbaikan')) {
                $table->string('hasil_perbaikan')->nullable()->index()->after('uraian_kegiatan');
            }

            if (! Schema::hasColumn('mfk_pemeliharaans', 'keterangan_tambahan')) {
                $table->text('keterangan_tambahan')->nullable()->after('hasil_perbaikan');
            }

            if (! Schema::hasColumn('mfk_pemeliharaans', 'biaya')) {
                $table->decimal('biaya', 15, 2)->default(0)->after('keterangan_tambahan');
            }

            if (! Schema::hasColumn('mfk_pemeliharaans', 'petugas')) {
                $table->string('petugas')->nullable()->after('biaya');
            }

            if (! Schema::hasColumn('mfk_pemeliharaans', 'foto_nota')) {
                $table->string('foto_nota')->nullable()->after('petugas');
            }

            if (! Schema::hasColumn('mfk_pemeliharaans', 'file_ba_perbaikan')) {
                $table->string('file_ba_perbaikan')->nullable()->after('foto_nota');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally no-op to avoid destructive rollback on repaired production data.
    }
};

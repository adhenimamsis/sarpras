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
        if (! Schema::hasTable('assets')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table): void {
            if (! Schema::hasColumn('assets', 'harga_perolehan')) {
                $table->decimal('harga_perolehan', 20, 2)->default(0)->after('kode_aspak');
            }

            if (! Schema::hasColumn('assets', 'status_ketersediaan')) {
                $table->string('status_ketersediaan')->default('Tersedia')->after('kondisi');
                $table->index('status_ketersediaan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no-op: this migration repairs schema drift after database restore.
    }
};

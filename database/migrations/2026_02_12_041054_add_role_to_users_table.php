<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom identitas dan hak akses ke tabel users yang sudah ada.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Menambahkan Kolom Role (Hak Akses)
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')
                    ->default('staff')
                    ->after('email')
                    ->index()
                    ->comment('admin, staff, kapus, teknisi');
            }

            // 2. Menambahkan Kolom Phone (Untuk integrasi WhatsApp Gateway)
            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')
                    ->nullable()
                    ->after('name')
                    ->comment('Nomor WA aktif untuk notifikasi');
            }

            // 3. Menambahkan Kolom Avatar (Foto Profil)
            if (! Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')
                    ->nullable()
                    ->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('users', 'role')) {
                $columns[] = 'role';
            }
            if (Schema::hasColumn('users', 'phone_number')) {
                $columns[] = 'phone_number';
            }
            if (Schema::hasColumn('users', 'avatar_url')) {
                $columns[] = 'avatar_url';
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

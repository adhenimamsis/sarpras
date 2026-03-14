<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kontrol akses, status aktif, dan profil ke tabel users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Kolom Role (Hak Akses)
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')
                    ->default('staff')
                    ->after('email')
                    ->index() // Penting untuk performa Filter Tab
                    ->comment('admin, staff, teknisi, kapus');
            }

            // 2. Kolom Status Aktif
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('role')
                    ->index();
            }

            // 3. Kolom Nomor HP (Untuk integrasi WA Gateway)
            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')
                    ->nullable()
                    ->after('name');
            }

            // 4. Kolom Avatar (Foto Profil)
            if (! Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')
                    ->nullable()
                    ->after('is_active');
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
            if (Schema::hasColumn('users', 'is_active')) {
                $columns[] = 'is_active';
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

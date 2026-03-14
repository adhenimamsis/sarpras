<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'group', // Untuk mengelompokkan (misal: 'identitas', 'api', 'notifikasi')
        'type',  // Tipe data: text, textarea, image, boolean
    ];

    /**
     * STRATEGI AUTO-CACHE:
     * Menghapus cache setiap kali data ditambah, diubah, atau dihapus.
     * Menggunakan event Eloquent agar sinkronisasi data Laporan & Dashboard instan.
     */
    protected static function booted()
    {
        $clearCache = function () {
            Cache::forget('global_settings');
        };

        static::saved($clearCache); // Covers created & updated
        static::deleted($clearCache);
    }

    /**
     * HELPER UTAMA: get()
     * Mengambil nilai setting berdasarkan Key dengan Cache Remember.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            // Ambil semua setting sekaligus dan simpan di cache selamanya
            $settings = Cache::rememberForever('global_settings', function () {
                return self::pluck('value', 'key')->all();
            });

            return $settings[$key] ?? $default;
        } catch (\Throwable $e) {
            // Fallback jika database/cache error saat migrasi
            return $default;
        }
    }

    /**
     * ALIAS HELPER: getValue()
     * Digunakan secara luas di LaporanController & Blade PDF.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return self::get($key, $default);
    }

    /**
     * HELPER: set()
     * Simpan atau Update nilai secara cepat.
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'group' => $group,
                'type' => $type,
            ]
        );

        return $setting;
    }

    /**
     * HELPER: Ambil semua setting berdasarkan Group (misal: 'identitas').
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)->pluck('value', 'key')->all();
    }
}

<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('get_setting')) {
    /**
     * Mengambil nilai pengaturan dari database dengan sistem Caching.
     *
     * * @param string $key
     */
    function get_setting($key, $default = null)
    {
        // Kita simpan di cache selama 24 jam (86400 detik)
        // Cache otomatis terhapus kalau Bos update data lewat Filament/Dashboard (jika Bos pasang logic clear cache)
        return Cache::remember("setting_{$key}", 86400, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }
}

/*
 * Fungsi tambahan: Clear cache setting jika Bos melakukan update data
 * Panggil fungsi ini di Controller setelah proses Save/Update setting
 */
if (! function_exists('clear_setting_cache')) {
    function clear_setting_cache($key)
    {
        Cache::forget("setting_{$key}");
    }
}

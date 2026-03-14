<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class StokOksigen extends Model
{
    use HasFactory;

    protected $table = 'stok_oksigens';

    protected $fillable = [
        'lokasi',
        'ukuran',
        'jumlah_masuk',
        'jumlah_keluar',
        'stok_akhir',
        'petugas',
        'keterangan',
    ];

    /**
     * Casting data agar perhitungan angka lebih akurat.
     */
    protected $casts = [
        'jumlah_masuk' => 'integer',
        'jumlah_keluar' => 'integer',
        'stok_akhir' => 'integer',
    ];

    /**
     * Booted function untuk logika otomatisasi saldo stok harian.
     */
    protected static function booted()
    {
        // 1. Logika saat data baru akan dibuat (Creating)
        static::creating(function ($model) {
            // Ambil saldo terakhir berdasarkan ukuran tabung (1m3 atau 6m3)
            $lastStock = static::where('ukuran', $model->ukuran)
                ->latest('id')
                ->value('stok_akhir') ?? 0;

            // Hitung saldo baru
            $newStock = $lastStock + $model->jumlah_masuk - $model->jumlah_keluar;

            // PROTEKSI: Jangan izinkan stok jadi negatif
            if ($newStock < 0) {
                throw ValidationException::withMessages(['jumlah_keluar' => "Stok tidak mencukupi! Sisa stok saat ini hanya {$lastStock} tabung."]);
            }

            $model->stok_akhir = $newStock;
        });

        // 2. Logika saat data diedit (Updating)
        static::updating(function ($model) {
            // Ambil saldo sebelum record ini dibuat (berdasarkan ID yang lebih kecil)
            $previousStock = static::where('ukuran', $model->ukuran)
                ->where('id', '<', $model->id)
                ->latest('id')
                ->value('stok_akhir') ?? 0;

            $newStock = $previousStock + $model->jumlah_masuk - $model->jumlah_keluar;

            if ($newStock < 0) {
                throw ValidationException::withMessages(['jumlah_keluar' => "Update gagal! Stok akan menjadi negatif ({$newStock}). Periksa kembali mutasi keluar."]);
            }

            $model->stok_akhir = $newStock;
        });
    }

    /**
     * HELPER: Ambil sisa stok saat ini (Global).
     */
    public static function currentStock($ukuran)
    {
        return static::where('ukuran', $ukuran)->latest('id')->value('stok_akhir') ?? 0;
    }

    /**
     * SCOPE: Filter pencarian.
     */
    public function scopeTabungBesar($query)
    {
        return $query->where('ukuran', '6m3');
    }

    public function scopeTabungKecil($query)
    {
        return $query->where('ukuran', '1m3');
    }
}

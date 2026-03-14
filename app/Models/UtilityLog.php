<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UtilityLog extends Model
{
    use HasFactory;

    protected $table = 'utility_logs';

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'tgl_catat',
        'jenis',           // listrik, air, ipal, solar, gas_medik
        'nama_meteran',    // Lokasi titik ukur
        'angka_meteran',   // Nilai yang tercatat
        'petugas',
        'catatan',
    ];

    /**
     * Casting data agar akurat saat perhitungan matematis.
     */
    protected $casts = [
        'tgl_catat' => 'date',
        'angka_meteran' => 'decimal:2',
    ];

    /**
     * BOOTED: Otomatisasi data petugas saat pembuatan data.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            // Otomatis isi nama petugas dari user yang login jika kosong
            if (Auth::check() && ! $model->petugas) {
                $model->petugas = Auth::user()->name;
            }
        });
    }

    /**
     * ACCESSOR: Menghitung Selisih Pemakaian dari record sebelumnya.
     * Sangat berguna untuk melihat konsumsi harian di Tabel/Dashboard.
     */
    public function getSelisihPemakaianAttribute(): float
    {
        $previousRecord = static::where('jenis', $this->jenis)
            ->where('nama_meteran', $this->nama_meteran)
            ->where('tgl_catat', '<', $this->tgl_catat)
            ->orderBy('tgl_catat', 'desc')
            ->first();

        if (! $previousRecord) {
            return 0;
        }

        return (float) ($this->angka_meteran - $previousRecord->angka_meteran);
    }

    /**
     * ACCESSOR: Mendapatkan Satuan berdasarkan Jenis Utilitas.
     */
    public function getSatuanAttribute(): string
    {
        return match ($this->jenis) {
            'listrik' => 'kWh',
            'air', 'ipal' => 'm3',
            'solar' => 'Liter',
            'gas_medik' => 'Bar/PSI',
            default => '',
        };
    }

    /**
     * HELPER: Mendapatkan daftar meteran unik untuk filter UI.
     */
    public static function getMeteranOptions(): array
    {
        return static::whereNotNull('nama_meteran')
            ->distinct()
            ->pluck('nama_meteran', 'nama_meteran')
            ->toArray();
    }

    /**
     * SCOPE: Filter pencarian berdasarkan jenis.
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'nama_ruangan',
        'kode_ruangan',
        'gedung',
        'lantai',
        'keterangan',
        'foto_denah',
        // Kolom koordinat untuk denah interaktif
        'koordinat_x',
        'koordinat_y',

        // --- FIELD TAMBAHAN LEGALITAS ---
        'status_tanah',
        'alamat_lokasi',
        'no_sertifikat',
        'penggunaan',
        'asal_usul',
    ];

    /**
     * Casting data agar Filament mengenali format file dan koordinat.
     */
    protected $casts = [
        'foto_denah' => 'array',
        'koordinat_x' => 'float',
        'koordinat_y' => 'float',
    ];

    /**
     * RELASI: Satu ruangan menampung banyak aset (Inventory).
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * ACCESSOR: Warna status dinamis untuk marker di denah atau dashboard.
     * Logika: Merah jika ada yang rusak berat, Kuning jika ada yang rusak ringan.
     */
    public function getStatusWarnaAttribute(): string
    {
        // Gunakan pluck/count untuk efisiensi query
        $kondisiAssets = $this->assets()->pluck('kondisi')->toArray();

        if (in_array('Rusak Berat', $kondisiAssets) || in_array('RB', $kondisiAssets)) {
            return 'danger'; // Merah
        }

        if (in_array('Rusak Ringan', $kondisiAssets) || in_array('RR', $kondisiAssets) || in_array('KB', $kondisiAssets)) {
            return 'warning'; // Kuning
        }

        return 'success'; // Hijau
    }

    /**
     * ACCESSOR: Format Nama Ruangan (Uppercase Words).
     */
    public function getNamaRuanganAttribute($value): string
    {
        return ucwords(strtolower($value));
    }

    /**
     * ACCESSOR: Menghitung total aset secara real-time.
     */
    public function getTotalAssetsAttribute(): int
    {
        return $this->assets()->count();
    }

    /**
     * ACCESSOR: Lokasi Lengkap untuk label Select/Dropdown.
     */
    public function getLokasiLengkapAttribute(): string
    {
        return "{$this->gedung} ({$this->lantai})";
    }

    /**
     * SCOPE: Filter berdasarkan Gedung.
     */
    public function scopeInGedung($query, $gedung)
    {
        return $query->where('gedung', $gedung);
    }
}

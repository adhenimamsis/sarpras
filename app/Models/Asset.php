<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    use HasFactory;

    /**
     * Eager Load otomatis untuk mencegah N+1 Query Problem pada Filament.
     */
    protected $with = ['ruangan'];

    protected $fillable = [
        // --- Identitas KIB (Kartu Inventaris Barang) ---
        'kategori_kib', 'no_register', 'nama_alat', 'kode_aspak', 'merk', 'tipe',
        'no_seri', 'no_sertifikat', 'luas_meter', 'alamat', 'asal_usul', 'konstruksi',
        'no_pbg_slf', 'no_ijin_edar',

        // --- Operasional & Lokasi ---
        'ruangan_id', 'lokasi_detail', 'tahun_perolehan', 'harga_perolehan',
        'kondisi', 'status_ketersediaan', 'is_active', 'catatan', 'foto', 'sertifikat',

        // --- MFK (Manajemen Fasilitas & Keselamatan) ---
        'kategori_utilitas', 'status_kalibrasi', 'tgl_kalibrasi_terakhir',
        'tgl_kalibrasi_selanjutnya', 'tgl_maintenance_terakhir',
        'tgl_maintenance_berikutnya', 'tgl_kadaluarsa', 'tgl_garansi_habis',
    ];

    /**
     * Casting data agar Laravel & Livewire mengenali format dengan benar.
     */
    protected $casts = [
        'status_kalibrasi' => 'boolean',
        'is_active' => 'boolean',
        'tgl_kalibrasi_terakhir' => 'date',
        'tgl_kalibrasi_selanjutnya' => 'date',
        'tgl_maintenance_terakhir' => 'date',
        'tgl_maintenance_berikutnya' => 'date',
        'tgl_kadaluarsa' => 'date',
        'tgl_garansi_habis' => 'date',
        'tahun_perolehan' => 'integer',
        'harga_perolehan' => 'double',
        'luas_meter' => 'double',
        'foto' => 'array',
        'sertifikat' => 'array',
    ];

    /**
     * APP LOGIC: STATUS WARNA UNTUK PIN PETA
     * Digunakan oleh Denah Interaktif untuk menentukan warna Pin.
     */
    public function getStatusWarnaAttribute(): string
    {
        if ($this->kondisi === 'Rusak Berat') {
            return 'red';
        }
        if ($this->kondisi === 'Rusak Ringan' || $this->butuhKalibrasiSegera()) {
            return 'yellow';
        }

        return 'green';
    }

    /**
     * LOGIKA MASA MANFAAT (DEPRECIATION POLICY)
     * Berdasarkan PMK Standar Akuntansi Pemerintah.
     */
    public function getMasaManfaat(): int
    {
        return match ($this->kategori_kib) {
            'KIB_B' => 5,  // Peralatan & Mesin (Alkes, PC, Kendaraan)
            'KIB_C' => 25, // Gedung & Bangunan
            'KIB_D' => 10, // Jalan, Irigasi & Jaringan
            'KIB_E' => 5,  // Aset Tetap Lainnya (Buku, Kesenian)
            default => 5,
        };
    }

    /**
     * ACCESSOR: Nilai Buku (Penyusutan Garis Lurus).
     */
    public function getNilaiBukuAttribute(): float
    {
        $harga = (float) $this->harga_perolehan;
        if (in_array($this->kategori_kib, ['KIB_A', 'KIB_F']) || $harga <= 0) {
            return $harga; // Tanah & KDP tidak disusutkan
        }

        $tahunSekarang = (int) date('Y');
        $tahunPerolehan = $this->tahun_perolehan ?: $tahunSekarang;
        $umurAset = max(0, $tahunSekarang - $tahunPerolehan);
        $masaManfaat = $this->getMasaManfaat();

        if ($umurAset >= $masaManfaat) {
            return 1.00;
        } // Nilai residu minimal

        $penyusutanPerTahun = $harga / $masaManfaat;

        return (float) ($harga - ($penyusutanPerTahun * $umurAset));
    }

    /**
     * HELPERS: Format Rupiah.
     */
    public function getHargaFormattedAttribute(): string
    {
        return 'Rp '.number_format($this->harga_perolehan, 0, ',', '.');
    }

    /**
     * RELASI TABEL.
     */
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function kalibrasis(): HasMany
    {
        return $this->hasMany(Kalibrasi::class);
    }

    public function pemeliharaans(): HasMany
    {
        return $this->hasMany(Pemeliharaan::class);
    }

    /**
     * Alias relasi untuk kompatibilitas controller/view lama.
     */
    public function maintenanceLogs(): HasMany
    {
        return $this->pemeliharaans();
    }

    public function laporan_kerusakans(): HasMany
    {
        return $this->hasMany(LaporanKerusakan::class);
    }

    /**
     * Alias relasi untuk gaya camelCase di layer presentasi.
     */
    public function laporanKerusakans(): HasMany
    {
        return $this->laporan_kerusakans();
    }

    public function penghapusan(): HasOne
    {
        return $this->hasOne(PenghapusanAsset::class);
    }

    public function mfks(): HasMany
    {
        return $this->hasMany(MfkPemeliharaan::class);
    }

    /**
     * SISTEM PERINGATAN (ALERTS)
     * Digunakan untuk Dashboard & Notifikasi.
     */
    public function butuhMaintenanceSegera(): bool
    {
        if (! $this->tgl_maintenance_berikutnya) {
            return false;
        }

        return Carbon::now()->diffInDays($this->tgl_maintenance_berikutnya, false) <= 14;
    }

    public function butuhKalibrasiSegera(): bool
    {
        if (! $this->status_kalibrasi || ! $this->tgl_kalibrasi_selanjutnya) {
            return false;
        }

        return Carbon::now()->diffInDays($this->tgl_kalibrasi_selanjutnya, false) <= 30;
    }

    public function isGaransiHabis(): bool
    {
        return $this->tgl_garansi_habis ? $this->tgl_garansi_habis->isPast() : false;
    }

    /**
     * SCOPE: Pencarian Cepat.
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->where('kondisi', '!=', 'Dihapuskan');
    }
}

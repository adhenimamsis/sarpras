<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Pemeliharaan extends Model
{
    use HasFactory;

    /**
     * Daftarkan semua kolom yang bisa diisi manual (Mass Assignment).
     */
    protected $fillable = [
        'asset_id',
        'user_id',            // Mencatat petugas login
        'tgl_pemeliharaan',   // Tanggal servis/jadwal
        'kegiatan',           // Nama kegiatan (sebelumnya deskripsi_kegiatan)
        'biaya',
        'status',             // Terjadwal, Selesai
        'petugas',            // Nama teknisi luar/vendor
        'keterangan',         // Catatan hasil teknis
    ];

    /**
     * Casting tipe data agar Filament mengenali format otomatis.
     */
    protected $casts = [
        'tgl_pemeliharaan' => 'date',
        'biaya' => 'integer',
    ];

    /**
     * BOOTED LOGIC:
     * Otomatisasi Sinkronisasi ke Tabel Asset saat pemeliharaan selesai.
     */
    protected static function booted()
    {
        static::saved(function ($pemeliharaan) {
            // Jika status diset 'Selesai', otomatis update master data aset
            if ($pemeliharaan->status === 'Selesai') {
                $asset = $pemeliharaan->asset;

                if ($asset) {
                    $asset->update([
                        'tgl_maintenance_terakhir' => $pemeliharaan->tgl_pemeliharaan,
                        // Otomatis set jadwal berikutnya 3 bulan (90 hari) ke depan
                        'tgl_maintenance_berikutnya' => $pemeliharaan->tgl_pemeliharaan->addDays(90),
                        'kondisi' => 'Baik',
                    ]);

                    Log::info("Pemeliharaan Sync: Aset {$asset->nama_alat} berhasil diupdate.");
                }
            }
        });
    }

    /**
     * Relasi ke Model Asset.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Relasi ke Model User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * ACCESSOR: Format Rupiah untuk Biaya.
     */
    public function getBiayaFormattedAttribute(): string
    {
        return 'Rp '.number_format($this->biaya ?? 0, 0, ',', '.');
    }

    /**
     * SCOPE: Filter data yang belum dikerjakan.
     */
    public function scopeBelumSelesai($query)
    {
        return $query->where('status', 'Terjadwal');
    }
}

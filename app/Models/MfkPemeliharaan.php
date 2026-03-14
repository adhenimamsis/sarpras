<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class MfkPemeliharaan extends Model
{
    use HasFactory;

    protected $table = 'mfk_pemeliharaans';

    protected $fillable = [
        'asset_id',
        'tgl_pemeliharaan',
        'tgl_berikutnya',
        'uraian_kegiatan',
        'hasil_perbaikan',
        'biaya',
        'petugas',
    ];

    /**
     * CASTING: Menjamin tipe data konsisten saat diolah.
     */
    protected $casts = [
        'tgl_pemeliharaan' => 'date',
        'tgl_berikutnya' => 'date',
        'biaya' => 'integer',
    ];

    /**
     * BOOTED: Otomatisasi sinkronisasi kondisi aset & pembuatan jadwal rutin.
     * Menggunakan booted() untuk Laravel versi terbaru agar lebih stabil.
     */
    protected static function booted()
    {
        // 1. LOGIKA SEBELUM SIMPAN (Creating/Updating)
        static::saving(function ($mfk) {
            if (empty($mfk->tgl_berikutnya) && ! empty($mfk->tgl_pemeliharaan)) {
                // Default: 90 hari (3 bulan) setelah pemeliharaan terakhir sesuai standar Puskesmas
                $mfk->tgl_berikutnya = Carbon::parse($mfk->tgl_pemeliharaan)->addDays(90);
            }
        });

        // 2. LOGIKA SETELAH SIMPAN (Saved: Create + Update)
        static::saved(function ($mfk) {
            $mfk->syncToAsset();
        });
    }

    /**
     * Sinkronisasi data ke tabel Master Asset secara otomatis.
     */
    public function syncToAsset()
    {
        $asset = $this->asset;

        if ($asset) {
            // Tentukan kondisi berdasarkan hasil perbaikan di lapangan
            $kondisiBaru = match ($this->hasil_perbaikan) {
                'Berfungsi' => 'B',  // Kode database: Baik
                'Rusak' => 'RB',      // Kode database: Rusak Berat
                'Perlu Kalibrasi' => 'KB', // Kode database: Kurang Baik / Rusak Ringan
                default => 'B',
            };

            // Update Master Data Asset
            $asset->update([
                'kondisi' => $kondisiBaru,
                'tgl_maintenance_terakhir' => $this->tgl_pemeliharaan,
                'tgl_maintenance_berikutnya' => $this->tgl_berikutnya,
            ]);

            Log::info("MFK Auto-Sync: Asset {$asset->nama_alat} updated successfully.");
        }
    }

    /**
     * Relasi ke Model Asset.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * ACCESSOR: Format Rupiah (panggil dengan $model->format_biaya).
     */
    public function getFormatBiayaAttribute(): string
    {
        return 'Rp '.number_format($this->biaya ?? 0, 0, ',', '.');
    }

    /**
     * SCOPE: Menampilkan data yang sudah jatuh tempo servis.
     */
    public function scopeJatuhTempo($query)
    {
        return $query->where('tgl_berikutnya', '<=', now());
    }

    /**
     * SCOPE: Menampilkan data pemeliharaan bulan ini.
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tgl_pemeliharaan', now()->month)
            ->whereYear('tgl_pemeliharaan', now()->year);
    }
}

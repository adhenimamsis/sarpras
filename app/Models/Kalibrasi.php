<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Kalibrasi extends Model
{
    protected $fillable = [
        'asset_id',
        'tgl_kalibrasi',
        'tgl_kadaluarsa',
        'pelaksana',
        'no_sertifikat',
        'hasil',
        'file_sertifikat',
    ];

    protected $casts = [
        'tgl_kalibrasi' => 'date',
        'tgl_kadaluarsa' => 'date',
    ];

    /**
     * RELASI: Kembali ke Asset.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * LOGIC OTOMATISASI:
     * Menghubungkan riwayat kalibrasi dengan Master Data Asset.
     */
    protected static function booted()
    {
        // Setiap kali ada input kalibrasi baru
        static::created(function ($kalibrasi) {
            $kalibrasi->updateAssetCalibration();
        });

        // Setiap kali data kalibrasi diupdate
        static::updated(function ($kalibrasi) {
            $kalibrasi->updateAssetCalibration();
        });
    }

    /**
     * Fungsi untuk sinkronisasi data ke tabel Assets.
     */
    public function updateAssetCalibration()
    {
        $asset = $this->asset;

        if ($asset) {
            $asset->update([
                'tgl_kalibrasi_terakhir' => $this->tgl_kalibrasi,
                'tgl_kalibrasi_selanjutnya' => $this->tgl_kadaluarsa,
            ]);

            // Log untuk audit trail sederhana
            Log::info("Asset ID: {$asset->id} updated via Calibration History.");
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class PenghapusanAsset extends Model
{
    // Mempertegas nama tabel agar sinkron dengan migrasi
    protected $table = 'penghapusan_assets';

    protected $fillable = [
        'asset_id',
        'tgl_penghapusan',
        'alasan',
        'no_sk',
        'metode',
        'keterangan',
        'foto_bukti',
    ];

    /**
     * Casting data agar format tanggal dan file dikenali Laravel/Filament.
     */
    protected $casts = [
        'tgl_penghapusan' => 'date',
    ];

    /**
     * Normalisasi foto_bukti:
     * - Kompatibel data lama JSON array.
     * - Kompatibel data baru string path (single upload).
     */
    public function getFotoBuktiAttribute(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return $value[0] ?? null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded[0] ?? null;
            }

            return $value;
        }

        return null;
    }

    /**
     * LOGIC OTOMATISASI:
     * Menjalankan perintah otomatis saat data penghapusan dibuat atau dihapus.
     */
    protected static function booted()
    {
        // 1. Saat data penghapusan dibuat (Created)
        static::created(function ($penghapusan) {
            $asset = $penghapusan->asset;
            if ($asset) {
                // Update kondisi aset di master tabel menjadi 'Dihapuskan'
                $asset->update([
                    'kondisi' => 'Dihapuskan',
                ]);

                Log::info("Aset ID: {$asset->id} telah otomatis di-set 'Dihapuskan' oleh sistem.");
            }
        });

        // 2. Saat data penghapusan dibatalkan (Deleted)
        static::deleted(function ($penghapusan) {
            $asset = $penghapusan->asset;
            if ($asset) {
                // Kembalikan kondisi aset menjadi 'Baik' jika catatan penghapusan dihapus
                $asset->update([
                    'kondisi' => 'Baik',
                ]);

                Log::warning("Penghapusan Aset ID: {$asset->id} dibatalkan. Status dikembalikan ke 'Baik'.");
            }
        });
    }

    /**
     * Relasi ke Model Asset.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}

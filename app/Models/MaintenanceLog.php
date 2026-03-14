<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'tanggal_servis',
        'jenis_tindakan',
        'teknisi',
        'detail_pekerjaan',
        'biaya',
        'file_nota',
    ];

    /**
     * Casting data.
     */
    protected $casts = [
        'tanggal_servis' => 'date',
        'biaya' => 'integer',
    ];

    /**
     * LOGIC OTOMATISASI:
     * Menghubungkan log servis dengan Master Data Asset & Pembersihan Storage.
     */
    protected static function booted()
    {
        // 1. Sinkronisasi Tanggal ke Tabel Assets saat servis dicatat/diubah
        static::saved(function ($log) {
            if ($log->asset) {
                $log->asset->update([
                    'tgl_maintenance_terakhir' => $log->tanggal_servis,
                    // Otomatis set jadwal berikutnya 6 bulan dari servis terakhir
                    'tgl_maintenance_berikutnya' => Carbon::parse($log->tanggal_servis)->addMonths(6),
                ]);
            }
        });

        // 2. Pembersihan File Nota saat data log dihapus
        static::deleting(function ($log) {
            if ($log->file_nota) {
                Storage::disk('public')->delete($log->file_nota);
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

    /**
     * ACCESSOR: Format Rupiah untuk Biaya.
     */
    public function getBiayaFormattedAttribute(): string
    {
        return 'Rp '.number_format($this->biaya, 0, ',', '.');
    }

    /**
     * SCOPE: Filter pencarian log berdasarkan nominal biaya tertentu.
     */
    public function scopeBiayaBesar($query, $nominal = 500000)
    {
        return $query->where('biaya', '>=', $nominal);
    }
}

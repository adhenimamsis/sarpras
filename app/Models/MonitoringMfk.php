<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MonitoringMfk extends Model
{
    use HasFactory;

    protected $table = 'monitoring_mfks';

    protected $fillable = [
        'jenis_utilitas',   // Listrik, Air, Gas Medik, APAR, IPAL, Bangunan
        'tgl_cek',
        'waktu_cek',
        'status',           // Normal, Gangguan, Perbaikan
        'petugas',          // Nama user yang memeriksa
        'parameter_cek',    // Catatan temuan lapangan
        'foto_bukti',       // Path file gambar
        'keterangan',       // Rencana Tindak Lanjut (RTL)

        // --- Data Detail (JSON/Array) ---
        'check_listrik',    // Detail tegangan/beban
        'detail_apar',      // Repeater unit APAR
        'kondisi_air',      // Checklist kualitas air
        'check_bangunan',   // Checklist fisik gedung
        'detail_ipal',      // Status inlet/aerasi/outlet
        'tekanan_oksigen',  // Angka PSI Gas Medik
        'stok_cadangan',    // Status ketersediaan tabung
    ];

    /**
     * Casting data JSON/Array sangat penting agar Filament
     * bisa membaca data CheckboxList dan Repeater.
     */
    protected $casts = [
        'tgl_cek' => 'date',
        // 'waktu_cek' => 'datetime', // Gunakan string jika format TimePicker murni jam
        'check_listrik' => 'array',
        'detail_apar' => 'array',
        'kondisi_air' => 'array',
        'check_bangunan' => 'array',
        'detail_ipal' => 'array',
    ];

    /**
     * Normalisasi foto_bukti:
     * - Data lama bisa tersimpan sebagai JSON array.
     * - Data baru dari FileUpload single file berupa string path.
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
     * BOOTED LOGIC:
     * Otomatisasi peringatan sistem jika ditemukan gangguan utilitas.
     */
    protected static function booted()
    {
        static::created(function ($monitoring) {
            if ($monitoring->status !== 'Normal') {
                Log::warning("MFK ALERT: Temuan {$monitoring->status} pada objek {$monitoring->jenis_utilitas} oleh {$monitoring->petugas}.");
            }
        });
    }

    /**
     * ACCESSOR: Label warna untuk UI Filament.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Normal' => 'success',
            'Gangguan' => 'danger',
            'Perbaikan' => 'warning',
            default => 'gray',
        };
    }

    /**
     * SCOPE: Filter data bermasalah (untuk dashboard widget).
     */
    public function scopeMembutuhkanTindakan($query)
    {
        return $query->whereIn('status', ['Gangguan', 'Perbaikan']);
    }

    /**
     * SCOPE: Monitoring hari ini.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tgl_cek', now());
    }
}

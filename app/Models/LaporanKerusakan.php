<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LaporanKerusakan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kerusakans';

    protected $fillable = [
        'asset_id',
        'pelapor',
        'deskripsi_kerusakan',
        'status',
        'tgl_lapor',
        'tgl_selesai',
        'tindakan_perbaikan',
        'teknisi_penanggung_jawab',
    ];

    protected $casts = [
        'tgl_lapor' => 'date',
        'tgl_selesai' => 'date',
    ];

    /**
     * Sinkronisasi status aset dan notifikasi WA.
     */
    protected static function booted(): void
    {
        static::created(function (self $laporan): void {
            $asset = $laporan->asset;
            if (! $asset) {
                return;
            }

            $asset->update(['kondisi' => 'Rusak Ringan']);

            $namaAlat = $asset->nama_alat ?? 'Aset tidak diketahui';
            $ruangan = $asset->ruangan->nama_ruangan ?? '-';
            $tglLapor = $laporan->tgl_lapor?->format('d/m/Y') ?? now()->format('d/m/Y');

            $pesan = "[SIMSARPRAS] Laporan Kerusakan Baru\n\n"
                ."Alat: {$namaAlat}\n"
                ."Lokasi: {$ruangan}\n"
                ."Pelapor: {$laporan->pelapor}\n"
                ."Detail: {$laporan->deskripsi_kerusakan}\n"
                ."Tanggal: {$tglLapor}\n"
                .'Status Aset: Rusak Ringan';

            static::sendWA(null, $pesan);
        });

        static::updated(function (self $laporan): void {
            if (! $laporan->wasChanged('status') || $laporan->status !== 'Selesai') {
                return;
            }

            $asset = $laporan->asset;
            if ($asset) {
                $asset->update(['kondisi' => 'Baik']);
            }

            $namaAlat = $asset?->nama_alat ?? 'Aset';
            $tindakan = $laporan->tindakan_perbaikan ?: '-';

            $pesan = "[SIMSARPRAS] Perbaikan Selesai\n\n"
                ."Alat: {$namaAlat}\n"
                ."Pelapor: {$laporan->pelapor}\n"
                ."Tindakan: {$tindakan}\n"
                .'Status Aset: Baik';

            static::sendWA(null, $pesan);
        });
    }

    /**
     * Kirim notifikasi WA via Fonnte.
     */
    public static function sendWA(?string $target, string $pesan): array|bool
    {
        if (! static::isWhatsAppEnabled()) {
            return false;
        }

        $token = trim((string) Setting::getValue('wa_token', config('services.fonnte.token')));
        $target = $target ?: static::resolveAdminTarget();

        if ($token === '' || ! $target) {
            Log::warning('Notifikasi WA dilewati: token atau target tidak tersedia.');

            return false;
        }

        $baseUrl = rtrim((string) config('services.fonnte.base_url', 'https://api.fonnte.com'), '/');

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(15)->asForm()->post($baseUrl.'/send', [
                'target' => $target,
                'message' => $pesan,
                'delay' => '2',
            ]);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Gagal kirim WA Fonnte: '.$e->getMessage());

            return false;
        }
    }

    protected static function resolveAdminTarget(): ?string
    {
        $target = trim((string) Setting::getValue('wa_admin_notif', config('services.fonnte.default_target')));

        return $target !== '' ? $target : null;
    }

    protected static function isWhatsAppEnabled(): bool
    {
        $value = Setting::getValue('enable_wa_notif', config('services.fonnte.enabled', false));

        return static::toBoolean($value);
    }

    protected static function toBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    /**
     * Relasi ke tabel Asset.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}

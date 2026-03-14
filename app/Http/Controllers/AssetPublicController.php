<?php

namespace App\Http\Controllers;

use App\Models\Asset;

class AssetPublicController extends Controller
{
    /**
     * Menampilkan informasi aset untuk publik/petugas lapangan (hasil scan QR Code).
     * Dioptimalkan untuk tampilan mobile saat discan menggunakan smartphone.
     */
    public function show(string $identifier)
    {
        // Cari aset dengan pencarian fleksibel (ID, Kode ASPAK, atau Nomor Register).
        $asset = Asset::with([
            'ruangan',
            'maintenanceLogs' => function ($query) {
                $query->latest()->limit(5);
            },
            'laporanKerusakans' => function ($query) {
                $query->where('status', '!=', 'Selesai')->latest();
            },
        ])
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                    ->orWhere('kode_aspak', $identifier)
                    ->orWhere('no_register', $identifier);
            })
            ->firstOrFail();

        // Hitung umur aset secara dinamis.
        $umurAset = $asset->tahun_perolehan
            ? date('Y') - $asset->tahun_perolehan
            : null;

        return view('public.asset-detail', [
            'asset' => $asset,
            'umur_aset' => $umurAset,
            'title' => 'Detail Aset: '.$asset->nama_alat,
        ]);
    }
}

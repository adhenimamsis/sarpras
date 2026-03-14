<?php

namespace App\Http\Controllers;

use App\Models\PenghapusanAsset;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenghapusanAssetController extends Controller
{
    /**
     * Fungsi untuk mencetak Berita Acara Penghapusan (BAP) satuan.
     * Output berupa dokumen formal untuk bukti penghapusan barang milik daerah.
     */
    public function print(PenghapusanAsset $record): View
    {
        // 1. Eager load relasi asset dan ruangan agar data lokasi muncul di BAP
        $record->load(['asset.ruangan']);

        // 2. Ambil Identitas Puskesmas & Pejabat secara aman
        $config = [
            'nama_puskesmas' => Setting::where('key', 'nama_puskesmas')->first()?->value ?? 'UPT PUSKESMAS BENDAN',
            'alamat' => Setting::where('key', 'alamat_puskesmas')->first()?->value,
            'kepala_pkm' => Setting::where('key', 'nama_kapus')->first()?->value ?? '....................',
            'nip_kapus' => Setting::where('key', 'nip_kapus')->first()?->value ?? '....................',
            'pengurus_barang' => Setting::where('key', 'nama_pengurus_barang')->first()?->value ?? '....................',
        ];

        // 3. Pastikan view tersedia di: resources/views/cetak/penghapusan-single.blade.php
        return view('cetak.penghapusan-single', [
            'record' => $record,
            'config' => $config,
            'tgl_cetak' => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }

    /**
     * Fungsi untuk mencetak rekapitulasi penghapusan aset.
     * Berguna untuk laporan tahunan inventarisasi aset ke Dinas Kesehatan.
     */
    public function cetakRekap(Request $request): View
    {
        // 1. Ambil data dengan relasi lengkap
        $query = PenghapusanAsset::with(['asset.ruangan']);

        // 2. Filter berdasarkan tahun (opsional, jika ada request dari dashboard)
        if ($request->filled('year')) {
            $query->whereYear('tgl_penghapusan', $request->year);
        }

        /** @var \Illuminate\Database\Eloquent\Collection $data */
        $data = $query->latest('tgl_penghapusan')->get();

        // 3. Metadata laporan
        $config = [
            'nama_puskesmas' => Setting::where('key', 'nama_puskesmas')->first()?->value ?? 'UPT PUSKESMAS BENDAN',
            'kepala_pkm' => Setting::where('key', 'nama_kapus')->first()?->value ?? '....................',
            'nip_kapus' => Setting::where('key', 'nip_kapus')->first()?->value ?? '....................',
        ];

        return view('cetak.penghapusan-rekap', [
            'data' => $data,
            'config' => $config,
            'tgl_cetak' => Carbon::now()->translatedFormat('d F Y'),
            'total_nilai' => $data->sum(fn ($item) => $item->asset->harga_perolehan ?? 0),
        ]);
    }
}

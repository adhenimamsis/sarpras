<?php

namespace App\Http\Controllers;

use App\Models\StokOksigen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StokOksigenController extends Controller
{
    /**
     * Generate oxygen stock report as PDF.
     */
    public function cetakLaporan(): Response|RedirectResponse
    {
        try {
            $riwayat = StokOksigen::query()
                ->latest('created_at')
                ->get();

            $stokTerakhirBesar = StokOksigen::query()
                ->where('ukuran', '6m3')
                ->latest('id')
                ->value('stok_akhir') ?? 0;

            $stokTerakhirKecil = StokOksigen::query()
                ->where('ukuran', '1m3')
                ->latest('id')
                ->value('stok_akhir') ?? 0;

            return Pdf::loadView('cetak.stok-oksigen', [
                'title' => 'Laporan Stok Oksigen',
                'tanggal' => now()->translatedFormat('d F Y H:i'),
                'riwayat' => $riwayat,
                'stok_terakhir_besar' => $stokTerakhirBesar,
                'stok_terakhir_kecil' => $stokTerakhirKecil,
            ])
                ->setPaper('a4', 'portrait')
                ->stream('Laporan-Stok-Oksigen-'.now()->format('Ymd-His').'.pdf');
        } catch (\Throwable $e) {
            Log::error('Gagal cetak laporan stok oksigen: '.$e->getMessage());

            return back()->with('error', 'Gagal generate laporan stok oksigen.');
        }
    }
}

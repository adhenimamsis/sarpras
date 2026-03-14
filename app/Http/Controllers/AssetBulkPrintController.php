<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssetBulkPrintController extends Controller
{
    /**
     * Menangani cetak label QR secara massal.
     * Dipanggil dari BulkAction di Filament AssetResource.
     */
    public function printSelected(Request $request)
    {
        // 1. Validasi input: pastikan ada parameter 'ids'
        if (! $request->filled('ids')) {
            return redirect()->back()->with('error', 'Pilih aset yang ingin dicetak terlebih dahulu, Bos!');
        }

        try {
            // 2. Konversi string IDs (misal: "1,2,3") menjadi array
            $ids = explode(',', $request->ids);

            // 3. Ambil data aset dengan Eager Loading
            // Kita ambil relasi 'ruangan' agar nama lokasi muncul di label tanpa lambat (N+1 issue)
            $assets = Asset::with(['ruangan'])
                ->whereIn('id', $ids)
                ->get();

            // 4. Cek ketersediaan data
            if ($assets->isEmpty()) {
                return response()->json([
                    'message' => 'Data aset tidak ditemukan atau sudah dihapus.',
                ], 404);
            }

            // 5. Catat log pencetakan (Opsional: untuk audit sarpras)
            Log::info('User '.auth()->user()->name.' mencetak '.$assets->count().' label aset.');

            // 6. Tampilkan View Cetak
            // File view: resources/views/print-label.blade.php
            return view('print-label', [
                'assets' => $assets,
                'print_date' => now()->translatedFormat('d F Y H:i'),
                'title' => 'Cetak Label Aset Massal',
            ]);
        } catch (\Exception $e) {
            // Jika ada error (misal ID bukan angka), log dan beri feedback
            Log::error('Gagal cetak massal: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyiapkan label.');
        }
    }
}

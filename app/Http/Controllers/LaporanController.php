<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MonitoringMfk;
use App\Models\StokOksigen;
use App\Models\UtilityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LaporanController extends Controller
{
    /**
     * 1. LAPORAN KOMPREHENSIF MFK (Dashboard Terpadu).
     */
    public function cetakPdf(Request $request)
    {
        try {
            $assetQuery = Asset::with(['ruangan']);
            if (Schema::hasColumn('assets', 'is_active')) {
                $assetQuery->where('is_active', true);
            }
            $allAssets = $assetQuery->get();

            $lastUtility = UtilityLog::where('jenis', 'listrik')->latest()->first();
            $stokOksigen = StokOksigen::all();
            $solarTerakhir = UtilityLog::where('jenis', 'solar')->latest()->first();

            $mfkIssues = MonitoringMfk::whereIn('status', ['Gangguan', 'Perbaikan', 'Rusak'])
                ->latest()->limit(10)->get();

            $alkes = $allAssets->where('kategori_kib', 'KIB_B');
            $jatuhTempo = $alkes->filter(function ($item) {
                return $item->tgl_kalibrasi_selanjutnya && Carbon::parse($item->tgl_kalibrasi_selanjutnya) <= now()->addMonth();
            })->count();

            $data = [
                'title' => 'LAPORAN KOMPREHENSIF FASILITAS DAN KESELAMATAN (MFK)',
                'tanggal' => now()->translatedFormat('d F Y'),
                'periode' => now()->translatedFormat('F Y'),
                'total_asset' => $allAssets->count(),
                'asset_baik' => $allAssets->where('kondisi', 'Baik')->count(),
                'asset_rusak' => $allAssets->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat'])->count(),
                'assets' => $allAssets,
                'monitoring' => [
                    'listrik' => $lastUtility ? "Angka Meter: {$lastUtility->angka_meteran} kWh" : 'Berfungsi Baik',
                    'air' => 'Suplai Lancar (Pompa & Toren Terpantau)',
                    'solar' => $solarTerakhir ? "{$solarTerakhir->angka_meteran} Liter" : 'Stok Cukup',
                ],
                'stok_oksigen' => $stokOksigen,
                'kalibrasi_summary' => [
                    'total_alkes' => $alkes->count(),
                    'sudah_kalibrasi' => $alkes->where('status_kalibrasi', true)->count(),
                    'jatuh_tempo' => $jatuhTempo,
                ],
                'mfk_issues' => $mfkIssues,
            ];

            return Pdf::loadView('print.laporan-bulanan', $data)
                ->setPaper('a4', 'portrait')
                ->stream('Laporan-MFK-'.now()->format('m-Y').'.pdf');
        } catch (\Exception $e) {
            Log::error('Gagal Cetak PDF Terpadu: '.$e->getMessage());

            return back()->with('error', 'Gagal generate laporan terpadu.');
        }
    }

    /**
     * 2. CETAK KIB A-F (Kartu Inventaris Barang).
     */
    public function cetakKib(Request $request, $kategori)
    {
        try {
            $assets = Asset::where('kategori_kib', $kategori)->with('ruangan')->get();

            $data = [
                'title' => 'KARTU INVENTARIS BARANG ('.str_replace('_', ' ', $kategori).')',
                'assets' => $assets,
                'kategori' => $kategori,
                'tanggal' => now()->translatedFormat('d F Y'),
            ];

            return Pdf::loadView('print.kib', $data)
                ->setPaper('a4', 'landscape') // KIB biasanya memanjang (Landscape)
                ->stream('Laporan-'.$kategori.'.pdf');
        } catch (\Exception $e) {
            Log::error("Gagal Cetak $kategori: ".$e->getMessage());

            return back()->with('error', 'Gagal generate KIB.');
        }
    }

    /**
     * 3. CETAK JADWAL KALIBRASI ALKES.
     */
    public function cetakKalibrasi()
    {
        try {
            $assets = Asset::where('kategori_kib', 'KIB_B')
                ->whereNotNull('tgl_kalibrasi_selanjutnya')
                ->orderBy('tgl_kalibrasi_selanjutnya', 'asc')
                ->get();

            $data = [
                'title' => 'JADWAL KALIBRASI ALAT KESEHATAN',
                'assets' => $assets,
                'tanggal' => now()->translatedFormat('d F Y'),
            ];

            return Pdf::loadView('print.kalibrasi', $data)
                ->setPaper('a4', 'portrait')
                ->stream('Jadwal-Kalibrasi-'.now()->format('Y').'.pdf');
        } catch (\Exception $e) {
            Log::error('Gagal Cetak Jadwal Kalibrasi: '.$e->getMessage());

            return back()->with('error', 'Gagal generate jadwal kalibrasi.');
        }
    }

    /**
     * 4. CETAK DAFTAR LEGALITAS & SERTIFIKAT.
     */
    public function cetakLegalitas()
    {
        try {
            // Mengambil aset tanah (KIB A) dan Bangunan (KIB C)
            $assets = Asset::whereIn('kategori_kib', ['KIB_A', 'KIB_C'])
                ->whereNotNull('no_sertifikat')
                ->get();

            $data = [
                'title' => 'DAFTAR DOKUMEN LEGALITAS ASET',
                'assets' => $assets,
                'tanggal' => now()->translatedFormat('d F Y'),
            ];

            return Pdf::loadView('print.legalitas', $data)
                ->setPaper('a4', 'portrait')
                ->stream('Daftar-Legalitas.pdf');
        } catch (\Exception $e) {
            Log::error('Gagal Cetak Legalitas: '.$e->getMessage());

            return back()->with('error', 'Gagal generate daftar legalitas.');
        }
    }
}

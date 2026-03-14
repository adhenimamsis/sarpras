<?php

namespace App\Http\Controllers;

use App\Models\LaporanKerusakan;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanKerusakanController extends Controller
{
    /**
     * Cetak rekap laporan kerusakan aset secara massal.
     */
    public function cetakRekap(Request $request, string $status = 'semua'): View
    {
        $query = LaporanKerusakan::with('asset.ruangan');

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('tgl_lapor', $request->integer('month'));
        }

        if ($request->filled('year')) {
            $query->whereYear('tgl_lapor', $request->integer('year'));
        }

        $laporan = $query->latest('tgl_lapor')->get();

        $selectedMonth = $request->filled('month') ? $request->integer('month') : null;
        $selectedYear = $request->filled('year') ? $request->integer('year') : now()->year;
        $periode = $selectedMonth
            ? Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y')
            : 'Semua Periode';

        $config = [
            'nama_puskesmas' => Setting::getValue('nama_puskesmas', 'UPT PUSKESMAS BENDAN'),
            'kepala_puskesmas' => Setting::getValue('nama_kapus'),
            'nip_kapus' => Setting::getValue('nip_kapus'),
            'tgl_cetak' => now()->translatedFormat('d F Y'),
            'periode' => $periode,
        ];

        return view('reports.kerusakan', [
            'data' => $laporan,
            'status' => $status,
            'config' => $config,
            'total_data' => $laporan->count(),
        ]);
    }

    /**
     * Cetak lembar instruksi kerja perbaikan aset.
     */
    public function print(LaporanKerusakan $record): View
    {
        $record->load('asset.ruangan');

        return view('reports.kerusakan-single', [
            'record' => $record,
            'nama_puskesmas' => Setting::getValue('nama_puskesmas', 'UPT PUSKESMAS BENDAN'),
            'tgl_cetak' => now()->translatedFormat('d F Y'),
        ]);
    }
}

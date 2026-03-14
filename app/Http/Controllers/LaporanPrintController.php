<?php

namespace App\Http\Controllers;

use App\Models\LaporanKerusakan;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPrintController extends Controller
{
    /**
     * Mengolah data laporan kerusakan untuk tampilan cetak.
     */
    public function cetakKerusakan(Request $request): View
    {
        $query = LaporanKerusakan::with('asset.ruangan');

        if ($request->filled('status') && $request->status !== 'all') {
            $statusMap = [
                'baru' => 'Lapor',
                'proses' => 'Proses',
                'selesai' => 'Selesai',
            ];

            if (array_key_exists($request->status, $statusMap)) {
                $query->where('status', $statusMap[$request->status]);
            }
        }

        $data = $query->orderByDesc('tgl_lapor')->get();

        return view('print-laporan-kerusakan', [
            'data' => $data,
            'nama_puskesmas' => Setting::getValue('nama_puskesmas', 'UPT PUSKESMAS BENDAN'),
            'alamat' => Setting::getValue('alamat_puskesmas', '-'),
            'title' => 'LAPORAN REKAPITULASI KERUSAKAN ASET',
            'sub_title' => $request->filled('status') && $request->status !== 'all'
                ? 'Status: '.strtoupper((string) $request->status)
                : 'Seluruh Periode',
            'tgl_cetak' => Carbon::now()->translatedFormat('d F Y H:i'),
            'pejabat' => Setting::getValue('nama_pengurus_barang', '....................'),
            'nip_pejabat' => Setting::getValue('nip_pengurus_barang', '....................'),
        ]);
    }
}

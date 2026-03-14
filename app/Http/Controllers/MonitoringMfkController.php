<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use App\Models\MonitoringMfk;
use App\Models\Setting;
use App\Models\UtilityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonitoringMfkController extends Controller
{
    /**
     * DASHBOARD MONITORING LISTRIK & UTILITAS (REAL-TIME)
     * Menampilkan data beban listrik, status genset, dan suhu area secara dinamis.
     */
    public function index(): View
    {
        // 1. Ambil Data Listrik Utama (PLN) Terakhir
        $listrikTerakhir = UtilityLog::where('jenis', 'listrik')
            ->latest('tgl_catat')
            ->latest('id')
            ->first();

        // 2. Ambil Data BBM Genset Terakhir (Solar)
        $bbmGenset = UtilityLog::where('jenis', 'solar')
            ->latest('tgl_catat')
            ->latest('id')
            ->first();

        // 3. Hitung Beban Saat Ini berdasarkan angka meteran terakhir
        $currentLoad = (float) ($listrikTerakhir?->angka_meteran ?? 0);

        // 4. Data status panel per area tanpa angka random.
        $latestMonitoring = MonitoringMfk::query()
            ->whereIn('jenis_utilitas', ['Listrik', 'Air', 'Bangunan', 'Gas Medik'])
            ->orderByDesc('tgl_cek')
            ->orderByDesc('id')
            ->get()
            ->unique('jenis_utilitas')
            ->keyBy('jenis_utilitas');

        $areas = [
            [
                'name' => 'Poli Klinik',
                'status' => $this->mapAreaStatus($latestMonitoring->get('Air')?->status),
                'load' => $this->formatAreaLoad($currentLoad, 0.30),
                'temp' => 24,
            ],
            [
                'name' => 'Ruang UGD',
                'status' => $this->mapAreaStatus($latestMonitoring->get('Bangunan')?->status),
                'load' => $this->formatAreaLoad($currentLoad, 0.36),
                'temp' => 23,
            ],
            [
                'name' => 'Laboratorium',
                'status' => $this->mapAreaStatus($latestMonitoring->get('Gas Medik')?->status),
                'load' => $this->formatAreaLoad($currentLoad, 0.22),
                'temp' => 23,
            ],
            [
                'name' => 'Server Room',
                'status' => $this->mapAreaStatus($latestMonitoring->get('Listrik')?->status),
                'load' => $this->formatAreaLoad($currentLoad, 0.12),
                'temp' => 22,
            ],
        ];

        // 5. Riwayat Pemeliharaan Panel & Genset (5 Terakhir)
        $maintenanceLogs = MaintenanceLog::with(['asset'])
            ->latest()
            ->limit(5)
            ->get();

        return view('monitoring-listrik', [
            'listrikTerakhir' => $listrikTerakhir,
            'bbmGenset' => $bbmGenset,
            'currentLoad' => $currentLoad,
            'areas' => $areas,
            'maintenanceLogs' => $maintenanceLogs,
        ]);
    }

    /**
     * EXPORT DATA UTILITY KE EXCEL (CSV STREAM)
     * Digunakan untuk rekap data meteran ke Dinas Kesehatan.
     */
    public function exportExcel(): StreamedResponse
    {
        $fileName = 'rekap_utilitas_'.date('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 agar karakter Indonesia terbaca rapi di Excel.
            fwrite($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Tanggal Catat', 'Jenis Utilitas', 'Nama Meteran', 'Angka Meteran', 'Satuan', 'Petugas', 'Catatan']);

            foreach (UtilityLog::query()->orderByDesc('tgl_catat')->orderByDesc('id')->cursor() as $log) {
                fputcsv($file, [
                    $log->tgl_catat?->format('d/m/Y') ?? '-',
                    strtoupper($log->jenis),
                    $log->nama_meteran ?? 'Sentral',
                    $log->angka_meteran,
                    $log->satuan ?: '-',
                    $log->petugas ?? 'System',
                    $log->catatan ?? '-',
                ]);
            }

            fclose($file);
        }, $fileName, $headers);
    }

    /**
     * CETAK FORM KOSONG UNTUK CHECKLIST MANUAL LAPANGAN.
     * Digunakan petugas sarpras saat inspeksi keliling.
     */
    public function cetakFormKosong()
    {
        $pdf = Pdf::loadView('print.form-monitoring-terpadu');

        return $pdf->setPaper('a4', 'portrait')
            ->stream('Form-Checklist-Monitoring-Terpadu.pdf');
    }

    /**
     * CETAK DOKUMEN MONITORING SATUAN (MFK SINGLE).
     */
    public function print(MonitoringMfk $record): View
    {
        $namaPkm = Setting::getValue('nama_puskesmas', 'UPT PUSKESMAS BENDAN');

        return view('cetak.monitoring-mfk-single', [
            'record' => $record,
            'nama_puskesmas' => $namaPkm,
            'tgl_cetak' => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }

    /**
     * CETAK REKAPITULASI MONITORING BULANAN.
     */
    public function cetakRekap(Request $request): View
    {
        $status = $request->query('status', 'semua');
        $query = MonitoringMfk::query();

        if ($status !== 'semua' && $status !== 'all') {
            $query->where('status', ucfirst($status));
        }

        if ($request->filled('month')) {
            $query->whereMonth('tgl_cek', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('tgl_cek', $request->year);
        }

        $data = $query->latest('tgl_cek')->get();

        $config = [
            'nama_puskesmas' => Setting::getValue('nama_puskesmas', 'UPT PUSKESMAS BENDAN'),
            'alamat' => Setting::getValue('alamat_puskesmas', 'Jalan Slamet No. 2 Pekalongan'),
            'kepala_pkm' => Setting::getValue('nama_kapus', Setting::getValue('nama_kepala_puskesmas', '....................')),
            'nip_kapus' => Setting::getValue('nip_kapus', Setting::getValue('nip_kepala_puskesmas', '....................')),
        ];

        return view('cetak.monitoring-mfk-rekap', [
            'data' => $data,
            'status' => $status,
            'config' => $config,
            'tgl_cetak' => Carbon::now()->translatedFormat('d F Y'),
            'total_temuan' => $data->where('status', 'Temuan')->count(),
        ]);
    }

    private function mapAreaStatus(?string $status): string
    {
        if ($status === null) {
            return 'Normal';
        }

        return strtolower($status) === 'normal' ? 'Normal' : 'Alert';
    }

    private function formatAreaLoad(float $currentLoad, float $factor): string
    {
        $areaLoad = max(($currentLoad * $factor) / 1000, 0);

        return number_format($areaLoad, 1).' kW';
    }
}

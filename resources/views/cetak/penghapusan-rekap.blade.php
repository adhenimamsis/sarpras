<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Penghapusan Aset - {{ $tgl_cetak }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4 landscape; margin: 1cm; }
            .no-print { display: none; }
            body { background-color: white !important; padding: 0 !important; }
        }
        body { font-family: 'serif'; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 6px; }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="no-print flex justify-center gap-4 mb-6">
        <button onclick="window.history.back()" class="bg-gray-600 text-white px-6 py-2 rounded shadow hover:bg-gray-700 transition font-bold text-sm">
            Kembali
        </button>
        <button onclick="window.print()" class="bg-green-700 text-white px-8 py-2 rounded shadow-xl hover:bg-green-800 transition font-bold text-sm">
            Cetak Laporan Rekap
        </button>
    </div>

    <div class="mx-auto bg-white p-10 border border-gray-300 shadow-sm min-h-screen">
        
        @include('components.kop-surat', ['compact' => true])

        <div class="text-center mb-6">
            <h3 class="text-xl font-bold uppercase underline">DAFTAR REKAPITULASI PENGHAPUSAN ASET / INVENTARIS</h3>
            <p class="text-sm font-semibold mt-1">Periode Laporan: S.D {{ $tgl_cetak }}</p>
        </div>

        <table class="text-[10px] w-full">
            <thead>
                <tr class="bg-gray-100 text-center uppercase font-bold">
                    <th class="w-8">No</th>
                    <th class="w-24">Tgl Hapus</th>
                    <th>Nama Barang / Aset</th>
                    <th class="w-32">Kode Aset (NUP)</th>
                    <th class="w-32">Merk/Type</th>
                    <th class="w-20 text-center">Tahun Perolehan</th>
                    <th>Alasan Penghapusan</th>
                    <th class="w-28 uppercase">Metode</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $row)
                <tr class="hover:bg-gray-50">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tgl_penghapusan)->format('d/m/Y') }}</td>
                    <td class="font-bold uppercase text-blue-900">{{ $row->asset->nama_alat ?? 'Aset Tidak Ditemukan' }}</td>
                    <td class="italic text-center">{{ $row->asset->kode_alat ?? '-' }}</td>
                    <td>{{ $row->asset->merk ?? '-' }}</td>
                    <td class="text-center">{{ $row->asset->tahun_perolehan ?? '-' }}</td>
                    <td class="text-red-700 font-semibold">{{ $row->alasan }}</td>
                    <td class="text-center font-bold">{{ strtoupper($row->metode) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-10 text-gray-500 italic text-sm">
                        Belum ada data penghapusan aset pada periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4 flex justify-between items-center text-[11px] font-bold uppercase">
            <span>Total Aset Dihapuskan: {{ $data->count() }} Item</span>
            <span class="italic text-gray-500 text-[10px]">Dicetak oleh: {{ auth()->user()->name }}</span>
        </div>

        <div class="grid grid-cols-2 mt-12 text-sm">
            <div class="text-center space-y-20">
                <div>
                    <p>Mengetahui,</p>
                    <p class="font-bold">Kepala UPT Puskesmas Bendan</p>
                </div>
                <div>
                    <p class="font-bold underline uppercase">( ............................................ )</p>
                    <p>NIP. ........................................</p>
                </div>
            </div>
            <div class="text-center space-y-20">
                <div>
                    <p>Pekalongan, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold uppercase">Pengurus Barang / MFK</p>
                </div>
                <div>
                    <p class="font-bold underline uppercase">{{ auth()->user()->name }}</p>
                    <p>NIP. ........................................</p>
                </div>
            </div>
        </div>

        <div class="mt-16 pt-4 border-t border-gray-200 text-[10px] text-gray-400 italic flex justify-between items-center no-print">
            <span>Sistem Informasi Inventaris Digital - UPT Puskesmas Bendan</span>
            <span>Generated: {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

</body>
</html>


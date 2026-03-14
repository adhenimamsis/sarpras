<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Laporan Kerusakan - {{ ucfirst($status) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4 landscape; margin: 1cm; }
            .no-print { display: none; }
            body { background-color: white !important; padding: 0 !important; }
        }
        body { font-family: 'serif'; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="no-print flex justify-center gap-4 mb-6">
        <button onclick="window.history.back()" class="bg-gray-600 text-white px-6 py-2 rounded shadow hover:bg-gray-700 transition font-bold text-sm">
            Kembali
        </button>
        <button onclick="window.print()" class="bg-blue-700 text-white px-8 py-2 rounded shadow-xl hover:bg-blue-800 transition font-bold text-sm">
            Cetak Rekapitulasi
        </button>
    </div>

    <div class="mx-auto bg-white p-10 border border-gray-300 shadow-sm min-h-screen">
        
        @include('components.kop-surat', ['compact' => true])

        <div class="text-center mb-6">
            <h3 class="text-xl font-bold uppercase underline">REKAPITULASI LAPORAN KERUSAKAN ASET</h3>
            <div class="flex justify-between items-center mt-2 px-2 text-xs font-semibold">
                <p>Status Filter: <span class="px-2 py-0.5 bg-black text-white rounded">{{ strtoupper($status) }}</span></p>
                <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
            </div>
        </div>

        <table class="text-[11px] w-full">
            <thead>
                <tr class="bg-gray-200 text-center uppercase font-bold">
                    <th class="w-10">No</th>
                    <th class="w-24">Tanggal Lapur</th>
                    <th class="w-48">Nama Alat / Aset</th>
                    <th class="w-32">Lokasi / Ruangan</th>
                    <th>Deskripsi Kerusakan</th>
                    <th class="w-24">Status</th>
                    <th class="w-32">Pelapor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $index => $item)
                <tr class="hover:bg-gray-50">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
                    <td class="font-bold uppercase text-blue-900">{{ $item->asset->nama_alat }}</td>
                    <td class="text-center">{{ $item->asset->ruangan->nama_ruangan ?? '-' }}</td>
                    <td>{{ $item->deskripsi_kerusakan }}</td>
                    <td class="text-center">
                        @php
                            $statusColor = match(strtolower($item->status)) {
                                'pending', 'dilaporkan' => 'text-red-600 font-bold',
                                'proses', 'perbaikan' => 'text-yellow-600 font-bold',
                                'selesai' => 'text-green-600 font-bold',
                                default => 'text-gray-600'
                            };
                        @endphp
                        <span class="{{ $statusColor }} uppercase">{{ $item->status }}</span>
                    </td>
                    <td class="text-center italic">{{ $item->user->name ?? 'Petugas' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-10 text-gray-500 italic text-sm">
                        Tidak ada data laporan kerusakan ditemukan untuk filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer Laporan --}}
        <div class="grid grid-cols-2 mt-12 text-sm">
            <div class="text-center space-y-20">
                <div>
                    <p>Mengetahui,</p>
                    <p class="font-bold uppercase">Kepala UPT Puskesmas Bendan</p>
                </div>
                <div>
                    <p class="font-bold underline uppercase">( ............................................ )</p>
                    <p class="text-xs">NIP. ........................................</p>
                </div>
            </div>
            <div class="text-center space-y-20">
                <div>
                    <p>Pekalongan, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold uppercase">Penanggung Jawab Sarpras</p>
                </div>
                <div>
                    <p class="font-bold underline uppercase">{{ auth()->user()->name }}</p>
                    <p class="text-xs">NIP. ........................................</p>
                </div>
            </div>
        </div>

        <div class="mt-16 pt-4 border-t border-gray-200 text-[10px] text-gray-400 italic flex justify-between items-center no-print">
            <span>Sistem Informasi Inventaris Digital - UPT Puskesmas Bendan</span>
            <span>Jumlah Data: {{ $laporan->count() }} Laporan</span>
        </div>
    </div>

</body>
</html>


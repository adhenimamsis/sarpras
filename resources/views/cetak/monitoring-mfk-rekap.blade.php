<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Monitoring MFK - {{ ucfirst($status) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4 landscape; margin: 1cm; }
            .no-print { display: none; }
            body { background-color: white !important; padding: 0 !important; }
        }
        body { font-family: 'sans-serif'; }
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="no-print flex justify-center gap-4 mb-6">
        <button onclick="window.history.back()" class="bg-gray-600 text-white px-6 py-2 rounded shadow hover:bg-gray-700 transition font-bold text-sm">
            Kembali
        </button>
        <button onclick="window.print()" class="bg-green-700 text-white px-8 py-2 rounded shadow-xl hover:bg-green-800 transition font-bold text-sm">
            Cetak Rekapitulasi
        </button>
    </div>

    <div class="mx-auto bg-white p-10 border border-gray-300 shadow-sm min-h-screen">
        
        @include('components.kop-surat', ['compact' => true])

        <div class="text-center mb-6">
            <h3 class="text-xl font-bold uppercase underline">REKAPITULASI PEMELIHARAAN UTILITAS (MFK)</h3>
            <div class="flex justify-between items-center mt-2 px-2 text-xs font-semibold">
                <p>Filter Status: <span class="px-2 py-0.5 bg-gray-800 text-white rounded">{{ strtoupper($status) }}</span></p>
                <p>Periode Cetak: {{ $tgl_cetak }}</p>
            </div>
        </div>

        <table class="w-full border-collapse border border-black text-[10px]">
            <thead>
                <tr class="bg-gray-100 text-center">
                    <th class="border border-black p-2 w-8">No</th>
                    <th class="border border-black p-2 w-32">Tanggal & Jam Cek</th>
                    <th class="border border-black p-2 w-48">Objek / Jenis Utilitas</th>
                    <th class="border border-black p-2">Parameter / Temuan Teknis</th>
                    <th class="border border-black p-2 w-24">Status Kontrol</th>
                    <th class="border border-black p-2 w-32">Petugas Pemeriksa</th>
                    <th class="border border-black p-2">Rekomendasi / RTL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $row)
                <tr class="hover:bg-gray-50">
                    <td class="border border-black p-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black p-2 text-center">
                        {{ \Carbon\Carbon::parse($row->tgl_cek)->translatedFormat('d/m/Y') }}<br>
                        <span class="text-gray-500 font-mono text-[9px]">{{ $row->waktu_cek }} WIB</span>
                    </td>
                    <td class="border border-black p-2 font-bold text-blue-900 uppercase">{{ $row->jenis_utilitas }}</td>
                    <td class="border border-black p-2">{{ $row->parameter_cek }}</td>
                    <td class="border border-black p-2 text-center">
                        @php
                            $color = match(strtolower($row->status)) {
                                'normal', 'berfungsi', 'baik' => 'text-green-700',
                                'rusak', 'bahaya' => 'text-red-700 font-bold',
                                default => 'text-yellow-600'
                            };
                        @endphp
                        <span class="{{ $color }}">{{ strtoupper($row->status) }}</span>
                    </td>
                    <td class="border border-black p-2 text-center">{{ $row->petugas }}</td>
                    <td class="border border-black p-2 italic {{ $row->keterangan ? 'text-red-800' : 'text-gray-400' }}">
                        {{ $row->keterangan ?? 'Tidak ada catatan khusus' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="border border-black p-8 text-center text-gray-500 italic text-sm">
                        Data monitoring tidak ditemukan untuk filter status ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer Laporan --}}
        <div class="mt-8 grid grid-cols-2 text-center text-sm">
            <div></div>
            <div class="space-y-16">
                <div>
                    <p>Pekalongan, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold">Penanggung Jawab MFK,</p>
                </div>
                <div>
                    <p class="font-bold underline uppercase">{{ auth()->user()->name }}</p>
                    <p>NIP. ........................................</p>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-4 border-t border-gray-200 text-[10px] text-gray-400 italic flex justify-between items-center">
            <span>Sistem Informasi Sarpras & MFK - UPT Puskesmas Bendan</span>
            <span>Halaman 1 dari 1 | Hash-ID: {{ md5($tgl_cetak) }}</span>
        </div>
    </div>
</body>
</html>


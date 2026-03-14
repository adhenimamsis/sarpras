<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Monitoring MFK - {{ $record->jenis_utilitas }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4; margin: 1cm; }
            .no-print { display: none; }
            body { background-color: white !important; padding: 0 !important; }
            .container-box { border: none !important; shadow: none !important; }
        }
        body { font-family: 'serif'; }
        .table-custom td, .table-custom th { border: 1px solid #000; padding: 10px; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="no-print flex justify-center gap-4 mb-6">
        <button onclick="window.history.back()" class="bg-gray-600 text-white px-6 py-2 rounded shadow hover:bg-gray-700 transition font-bold text-sm">
            Kembali
        </button>
        <button onclick="window.print()" class="bg-blue-600 text-white px-8 py-2 rounded shadow-lg hover:bg-blue-700 transition font-bold text-sm">
            Cetak Dokumen
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-10 border border-gray-300 shadow-sm container-box">
        @include('components.kop-surat')

        <h3 class="text-center text-xl font-bold underline uppercase mb-8">FORMULIR MONITORING MFK (UTILITAS)</h3>

        <div class="grid grid-cols-2 gap-4 mb-6 text-sm border-b border-gray-200 pb-4">
            <div class="space-y-1">
                <p><span class="font-semibold inline-block w-36">Objek Pemeriksaan</span>: <span class="uppercase font-bold text-blue-800">{{ $record->jenis_utilitas }}</span></p>
                <p><span class="font-semibold inline-block w-36">Tanggal Cek</span>: {{ \Carbon\Carbon::parse($record->tgl_cek)->translatedFormat('d F Y') }}</p>
            </div>
            <div class="text-right space-y-1">
                <p><span class="font-semibold">Waktu</span>: {{ $record->waktu_cek }} WIB</p>
                <p><span class="font-semibold">ID Pemeriksaan</span>: #MFK-{{ $record->id }}</p>
            </div>
        </div>

        <table class="w-full border-collapse table-custom mb-8 text-sm">
            <thead>
                <tr class="bg-gray-100 uppercase">
                    <th class="p-2 text-left w-1/3">Parameter Penilaian</th>
                    <th class="p-2 text-left">Hasil Temuan Lapangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-semibold bg-gray-50">Status Kondisi Utilitas</td>
                    <td class="p-2">
                        @php
                            $statusClass = match(strtolower($record->status)) {
                                'normal', 'berfungsi' => 'text-green-700 bg-green-50 border-green-200',
                                'rusak', 'bahaya' => 'text-red-700 bg-red-50 border-red-200',
                                default => 'text-yellow-700 bg-yellow-50 border-yellow-200'
                            };
                        @endphp
                        <span class="px-3 py-1 border font-bold rounded uppercase {{ $statusClass }}">
                            {{ $record->status }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="font-semibold bg-gray-50">Catatan/Temuan Teknis</td>
                    <td class="italic text-gray-700">{{ $record->parameter_cek }}</td>
                </tr>
                @if($record->keterangan)
                <tr>
                    <td class="font-semibold bg-gray-50 text-red-600 uppercase">Rencana Tindak Lanjut</td>
                    <td class="font-bold text-red-800">{{ $record->keterangan }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        @php
            $fotoBuktiDataUrl = \App\Support\Files\PublicImageDataUrl::fromPath($record->foto_bukti);
        @endphp
        @if($fotoBuktiDataUrl)
        <div class="mb-10 page-break-inside-avoid">
            <p class="font-bold mb-3 border-b border-black inline-block text-xs uppercase italic">Dokumentasi Lapangan:</p>
            <div class="mt-2">
                <img src="{{ $fotoBuktiDataUrl }}" 
                     class="w-80 h-auto border-4 border-gray-100 shadow-md rounded-sm">
            </div>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-10 mt-20 text-center">
            <div class="space-y-20">
                <p class="text-sm uppercase">Mengetahui,<br><span class="font-bold">Kepala UPT Puskesmas Bendan</span></p>
                <div>
                    <p class="font-bold underline uppercase">( ............................................ )</p>
                    <p class="text-xs">NIP. ........................................</p>
                </div>
            </div>
            <div class="space-y-20">
                <p class="text-sm uppercase">Pekalongan, {{ \Carbon\Carbon::parse($record->tgl_cek)->translatedFormat('d F Y') }}<br><span class="font-bold">Petugas Sarpras / MFK</span></p>
                <div>
                    <p class="font-bold underline uppercase">{{ $record->petugas }}</p>
                    <p class="text-xs">NIP/NRP. ........................................</p>
                </div>
            </div>
        </div>

        <div class="mt-24 pt-4 border-t border-gray-200 text-[10px] text-gray-400 italic flex justify-between items-center">
            <span>Sistem Monitoring MFK - UPT Puskesmas Bendan</span>
            <span>ID Log: {{ $record->id }} | Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }}</span>
        </div>
    </div>
</body>
</html>


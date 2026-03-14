<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kerusakan - {{ $record->asset->nama_alat ?? 'Aset' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4; margin: 1cm; }
            .no-print { display: none; }
            body { background-color: white !important; padding: 0 !important; }
            .container-box { border: none !important; shadow: none !important; }
        }
        body { font-family: 'serif'; line-height: 1.5; }
        .table-input td { border: 1px solid #000; padding: 8px; }
    </style>
</head>
<body class="bg-gray-100 p-6">

    <div class="no-print flex justify-center gap-4 mb-8">
        <button onclick="window.history.back()" class="bg-gray-600 text-white px-6 py-2 rounded-full shadow hover:bg-gray-700 transition font-bold text-sm">
            Kembali
        </button>
        <button onclick="window.print()" class="bg-blue-700 text-white px-8 py-3 rounded-full shadow-xl hover:bg-blue-800 transition font-bold text-sm">
            CETAK FORMULIR
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-10 border border-gray-300 shadow-sm min-h-[29.7cm] container-box">
        
        @include('components.kop-surat')

        <div class="text-center mb-8">
            <h3 class="text-xl font-bold underline uppercase">LAPORAN KERUSAKAN ASET</h3>
            <p class="text-sm font-semibold tracking-widest">No. Tiket: #{{ $record->id }}/LK/{{ date('m/Y') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8 text-sm border-y border-gray-200 py-4">
            <div class="space-y-2">
                <p><span class="inline-block w-32 font-semibold">Tanggal Lapor</span>: {{ $record->created_at->translatedFormat('d F Y') }}</p>
                <p><span class="inline-block w-32 font-semibold">Pelapor</span>: {{ $record->pelapor ?? 'Petugas' }}</p>
                <p><span class="inline-block w-32 font-semibold">Status</span>: 
                    <span class="px-2 py-0.5 bg-black text-white font-bold text-[10px] uppercase">{{ $record->status }}</span>
                </p>
            </div>
            <div class="space-y-2 text-right">
                <p><span class="font-semibold uppercase text-xs">Nama Aset:</span> {{ $record->asset->nama_alat ?? '-' }}</p>
                <p><span class="font-semibold uppercase text-xs">Kode Aset:</span> {{ $record->asset->kode_aspak ?? $record->asset->no_register ?? '-' }}</p>
                <p><span class="font-semibold uppercase text-xs">Ruangan:</span> {{ $record->asset->ruangan->nama_ruangan ?? '-' }}</p>
            </div>
        </div>

        <div class="mb-8">
            <h4 class="font-bold border-b border-black mb-2 uppercase text-xs tracking-wider">A. Deskripsi Kerusakan / Keluhan:</h4>
            <div class="p-4 border border-gray-300 bg-gray-50 min-h-[80px] italic text-gray-700">
                "{{ $record->deskripsi_kerusakan }}"
            </div>
        </div>

        @php
            $fotoKerusakanDataUrl = \App\Support\Files\PublicImageDataUrl::fromPath($record->foto_kerusakan);
        @endphp
        @if($fotoKerusakanDataUrl)
        <div class="mb-8">
            <h4 class="font-bold border-b border-black mb-2 uppercase text-xs tracking-wider">B. Foto Kondisi Kerusakan:</h4>
            <div class="border p-2 inline-block bg-gray-50 rounded">
                {{-- Menggunakan Base64 agar gambar muncul di PDF --}}
                <img src="{{ $fotoKerusakanDataUrl }}" class="w-64 h-auto shadow-sm rounded">
            </div>
        </div>
        @endif

        <div class="mb-10">
            <h4 class="font-bold border-b border-black mb-2 uppercase text-xs tracking-wider">C. Hasil Perbaikan & Suku Cadang (Oleh Teknisi):</h4>
            <table class="w-full table-input">
                <tr class="h-12">
                    <td class="w-1/3 bg-gray-50 font-semibold italic text-xs">Tindakan/Solusi</td>
                    <td class="text-sm">{{ $record->tindakan_perbaikan ?? '' }}</td>
                </tr>
                <tr class="h-12">
                    <td class="bg-gray-50 font-semibold italic text-xs">Suku Cadang Diganti</td>
                    <td></td>
                </tr>
                <tr class="h-12">
                    <td class="bg-gray-50 font-semibold italic text-xs">Tanggal Selesai</td>
                    <td class="text-sm">{{ $record->tgl_selesai ? \Carbon\Carbon::parse($record->tgl_selesai)->format('d/m/Y') : '' }}</td>
                </tr>
            </table>
        </div>

        {{-- Tanda Tangan --}}
        <div class="grid grid-cols-3 gap-4 mt-20 text-xs text-center">
            <div>
                <p class="mb-20">Pelapor,</p>
                <p class="font-bold underline uppercase">{{ $record->pelapor ?? 'Petugas' }}</p>
            </div>
            <div>
                <p class="mb-20">Teknisi / Maintenance,</p>
                <p class="font-bold underline uppercase">...........................</p>
            </div>
            <div>
                <p class="mb-20">Kepala Ruangan / Sarpras,</p>
                <p class="font-bold underline uppercase">...........................</p>
            </div>
        </div>

        <div class="mt-24 pt-4 border-t border-gray-200 text-[10px] text-gray-400 italic flex justify-between">
            <span>SIM SARPRAS Digital - UPT Puskesmas Bendan</span>
            <span>ID Log: {{ $record->id }} | Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }}</span>
        </div>
    </div>

</body>
</html>


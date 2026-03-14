<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penghapusan - {{ $record->asset->nama_alat }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4; margin: 1.5cm; }
            .no-print { display: none; }
            body { background-color: white !important; padding: 0 !important; }
        }
        /* Menggunakan font serif untuk kesan dokumen negara yang formal */
        body { font-family: 'serif'; line-height: 1.6; }
        .dotted-border { border-bottom: 1px dotted #000; }
    </style>
</head>
<body class="bg-gray-100 p-6">

    <div class="no-print flex justify-center gap-4 mb-8">
        <button onclick="window.history.back()" class="bg-gray-600 text-white px-6 py-2 rounded-full shadow hover:bg-gray-700 transition font-bold text-sm">
            Kembali
        </button>
        <button onclick="window.print()" class="bg-red-700 text-white px-8 py-3 rounded-full shadow-xl hover:bg-red-800 transition font-bold text-sm">
            CETAK BERITA ACARA
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-12 border border-gray-300 shadow-sm min-h-[29.7cm]">
        
        @include('components.kop-surat')

        <div class="text-center mb-10">
            <h3 class="text-xl font-bold underline uppercase">BERITA ACARA PENGHAPUSAN BARANG</h3>
            <p class="text-base font-semibold">Nomor: {{ $record->id }}/BAP-SARPRAS/{{ date('Y') }}</p>
        </div>

        <div class="text-sm mb-8 text-justify leading-relaxed">
            <p class="mb-4 text-base">
                Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($record->tgl_penghapusan)->translatedFormat('d F Y') }}</strong>, bertempat di UPT Puskesmas Bendan Kota Pekalongan, kami yang bertanda tangan di bawah ini telah melaksanakan pemeriksaan fisik dan penilaian teknis terhadap aset sebagai berikut:
            </p>

            <table class="w-full mb-8">
                <tr class="h-9">
                    <td class="w-48 font-semibold">Nama Barang / Aset</td>
                    <td class="w-4">:</td>
                    <td class="dotted-border font-bold uppercase text-lg text-blue-900">{{ $record->asset->nama_alat }}</td>
                </tr>
                <tr class="h-9">
                    <td class="font-semibold">Kode Aset / NUP</td>
                    <td>:</td>
                    <td class="dotted-border italic text-gray-700 font-mono">{{ $record->asset->kode_aspak ?? $record->asset->no_register ?? '-' }}</td>
                </tr>
                <tr class="h-9">
                    <td class="font-semibold">Merk / Type / Spesifikasi</td>
                    <td>:</td>
                    <td class="dotted-border">{{ $record->asset->merk ?? '-' }}</td>
                </tr>
                <tr class="h-9">
                    <td class="font-semibold">Tahun Perolehan</td>
                    <td>:</td>
                    <td class="dotted-border font-bold">{{ $record->asset->tahun_perolehan ?? '-' }}</td>
                </tr>
                <tr class="h-10">
                    <td class="font-bold text-red-700 uppercase">Alasan Penghapusan</td>
                    <td>:</td>
                    <td class="dotted-border border-red-300 font-bold uppercase text-red-800 bg-red-50 px-2 italic">{{ $record->alasan }}</td>
                </tr>
            </table>

            <p class="mb-4 text-base">
                Berdasarkan hasil pengecekan lapangan, aset tersebut dinyatakan telah memenuhi kriteria untuk dihapuskan dari daftar inventaris karena kondisi yang <strong>{{ $record->alasan }}</strong> sehingga sudah tidak dapat digunakan kembali secara aman dan efisien untuk menunjang pelayanan kesehatan di UPT Puskesmas Bendan.
            </p>
            <p class="mb-4 text-base font-medium">
                Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagai dasar proses administrasi penghapusan aset sesuai dengan ketentuan perundang-undangan yang berlaku.
            </p>
        </div>

        @php
            $fotoBuktiDataUrl = \App\Support\Files\PublicImageDataUrl::fromPath($record->foto_bukti);
        @endphp
        @if($fotoBuktiDataUrl)
        <div class="mb-12 page-break-inside-avoid">
            <p class="font-bold text-xs mb-3 italic text-gray-600 uppercase tracking-widest border-b inline-block">Lampiran Bukti Kondisi Fisik:</p>
            <div class="block">
                <div class="inline-block border-4 border-gray-100 p-1 rounded bg-white shadow-md">
                    <img src="{{ $fotoBuktiDataUrl }}" 
                         class="max-w-xs h-48 object-cover rounded shadow-inner">
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-20 mt-16 text-sm">
            <div class="text-center space-y-20">
                <div>
                    <p>Mengetahui,</p>
                    <p class="font-bold uppercase italic">Kepala UPT Puskesmas Bendan</p>
                </div>
                <div>
                    <p class="font-bold underline text-base uppercase">NAMA KEPALA PUSKESMAS, SKM</p>
                    <p class="text-xs">NIP. 198XXXXXXXXXXXXXXX</p>
                </div>
            </div>
            <div class="text-center space-y-20">
                <div>
                    <p>Pekalongan, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold uppercase italic text-center text-blue-900">Pengurus Barang / Inventaris</p>
                </div>
                <div>
                    <p class="font-bold underline text-base text-center uppercase">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-center">NIP. ........................................</p>
                </div>
            </div>
        </div>

        <div class="mt-20 pt-4 border-t border-gray-200 text-[10px] text-gray-400 italic flex justify-between uppercase tracking-widest no-print">
            <span>Sistem Informasi Inventaris Aset - UPT Puskesmas Bendan</span>
            <span>ID LOG: #{{ $record->id }} | Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }}</span>
        </div>
    </div>

</body>
</html>


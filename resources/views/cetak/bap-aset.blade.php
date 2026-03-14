<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BAP Penghapusan - {{ $record->asset->nama_alat }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; margin: 0; padding: 20px; }
        
        .judul-doc { text-align: center; text-decoration: underline; font-weight: bold; margin-top: 20px; font-size: 14pt; }
        .nomor-doc { text-align: center; margin-bottom: 30px; font-size: 12pt; }
        
        .pembukaan { text-align: justify; margin-bottom: 20px; text-indent: 40px; }
        
        .detail-aset { width: 100%; border: 1px solid #000; border-collapse: collapse; margin: 20px 0; font-size: 11pt; }
        .detail-aset th, .detail-aset td { border: 1px solid #000; padding: 8px; text-align: left; }
        .detail-aset th { background-color: #f2f2f2; text-align: center; font-weight: bold; }

        .data-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .data-table td { padding: 3px 5px; vertical-align: top; }

        .footer-sign { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        .footer-sign td { width: 50%; text-align: center; vertical-align: top; }
        .spacer { height: 70px; }
        
        .foto-box { margin-top: 30px; text-align: center; page-break-inside: avoid; border: 1px dashed #ccc; padding: 10px; }
        .foto-box img { max-width: 350px; height: auto; border: 1px solid #000; margin-top: 10px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2d3336; color: #fff; cursor: pointer; border: none; border-radius: 4px; font-weight: bold;">
            Cetak Berita Acara
        </button>
    </div>

    @include('components.kop-surat')

    <div class="judul-doc">BERITA ACARA PENGHAPUSAN BARANG INVENTARIS</div>
    <div class="nomor-doc">Nomor: {{ $record->no_sk ?? '..../BAP-MFK/..../2026' }}</div>

    <div class="pembukaan">
        Pada hari ini, <strong>{{ \Carbon\Carbon::parse($record->tgl_penghapusan)->translatedFormat('l') }}</strong> 
        tanggal <strong>{{ \Carbon\Carbon::parse($record->tgl_penghapusan)->translatedFormat('d') }}</strong> 
        bulan <strong>{{ \Carbon\Carbon::parse($record->tgl_penghapusan)->translatedFormat('F') }}</strong> 
        tahun <strong>{{ \Carbon\Carbon::parse($record->tgl_penghapusan)->translatedFormat('Y') }}</strong>, 
        bertempat di UPT Puskesmas Bendan, kami yang bertanda tangan di bawah ini telah melaksanakan proses penghapusan barang inventaris dengan rincian sebagai berikut:
    </div>

    <table class="detail-aset">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Kode Alat</th>
                <th>Merk / Tipe</th>
                <th>Thn Perolehan</th>
                <th>Alasan Penghapusan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $record->asset->nama_alat }}</td>
                <td>{{ $record->asset->kode_aspak ?? $record->asset->no_register ?? '-' }}</td>
                <td>{{ $record->asset->merk ?? '-' }}</td>
                <td style="text-align: center;">{{ $record->asset->tahun_perolehan }}</td>
                <td>{{ $record->alasan }}</td>
            </tr>
        </tbody>
    </table>

    <table class="data-table">
        <tr>
            <td width="25%">Metode Penghapusan</td>
            <td width="2%">:</td>
            <td><strong>{{ $record->metode }}</strong></td>
        </tr>
        <tr>
            <td>Keterangan Tambahan</td>
            <td>:</td>
            <td>{{ $record->keterangan ?? 'Barang sudah dalam kondisi rusak berat dan tidak ekonomis jika dilakukan perbaikan.' }}</td>
        </tr>
    </table>

    <div class="pembukaan" style="text-indent: 0;">
        Demikian Berita Acara ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagai bukti fisik penghapusan aset dan bahan laporan mutasi barang milik daerah.
    </div>

    {{-- FOTO BUKTI (Ditanam sebagai Base64 agar tidak hilang) --}}
    @php
        $fotoBuktiDataUrl = \App\Support\Files\PublicImageDataUrl::fromPath($record->foto_bukti);
    @endphp
    @if($fotoBuktiDataUrl)
    <div class="foto-box">
        <p style="margin: 0;"><strong>DOKUMENTASI FISIK BARANG:</strong></p>
        <img src="{{ $fotoBuktiDataUrl }}">
    </div>
    @endif

    <table class="footer-sign">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala UPT Puskesmas Bendan
                <div class="spacer"></div>
                <strong>( ............................................ )</strong><br>
                NIP. ........................................
            </td>
            <td>
                Pekalongan, {{ \Carbon\Carbon::parse($record->tgl_penghapusan)->translatedFormat('d F Y') }}<br>
                Pengurus Barang / Petugas MFK
                <div class="spacer"></div>
                <strong>( {{ auth()->user()->name }} )</strong><br>
                NIP. ........................................
            </td>
        </tr>
    </table>

</body>
</html>


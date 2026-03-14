<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penghapusan - {{ $penghapusan->asset->nama_alat }}</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            line-height: 1.6; 
            padding: 10px; 
            color: #000;
            background: #fff;
        }

        /* Judul Surat */
        .title-container { text-align: center; margin-bottom: 25px; }
        .title { 
            font-weight: bold; 
            text-decoration: underline; 
            font-size: 16px; 
            text-transform: uppercase;
            display: block;
        }
        .nomor-surat { font-size: 14px; margin-top: 5px; }

        .content { margin-top: 20px; font-size: 14px; text-align: justify; }
        
        /* Tabel Data Aset */
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { 
            border: 1px solid #000; 
            padding: 10px; 
            text-align: left; 
            vertical-align: top;
        }
        .table th { background-color: #f5f5f5; width: 35%; font-weight: bold; }

        /* Area Tanda Tangan */
        .footer { margin-top: 50px; width: 100%; page-break-inside: avoid; }
        .signature-wrapper { width: 100%; }
        .sig-box { width: 250px; text-align: center; }
        .sig-box.left { float: left; }
        .sig-box.right { float: right; }
        .spacer { height: 75px; }

        @media print { 
            .no-print { display: none; } 
            body { padding: 0; }
            @page { size: A4; margin: 2cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="background: #e9ecef; padding: 15px; margin-bottom: 30px; border-left: 5px solid #0d6efd; border-radius: 4px;">
        <h3 style="margin: 0 0 5px 0;">Pratinjau Berita Acara</h3>
        <p style="margin: 0 0 10px 0; font-size: 13px;">Gunakan kertas <b>A4</b> dengan orientasi <b>Potrait</b>. Pastikan margin printer sesuai.</p>
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #0d6efd; color: white; border: none; border-radius: 4px; font-weight: bold;">
            Cetak Sekarang
        </button>
    </div>

    @include('components.kop-surat')

    <div class="title-container">
        <span class="title">BERITA ACARA PENGHAPUSAN BARANG MILIK DAERAH</span>
        <div class="nomor-surat">Nomor: {{ $penghapusan->no_sk ?? '400.7.3.7 / ...... / 2026' }}</div>
    </div>

    <div class="content">
        <p>Pada hari ini <b>{{ \Carbon\Carbon::parse($penghapusan->tgl_penghapusan)->translatedFormat('l') }}</b> tanggal <b>{{ \Carbon\Carbon::parse($penghapusan->tgl_penghapusan)->translatedFormat('d F Y') }}</b>, bertempat di {{ \App\Models\Setting::getValue('nama_puskesmas') }}, kami yang bertanda tangan di bawah ini telah melaksanakan penghapusan aset sarana prasarana dengan rincian sebagai berikut:</p>
        
        <table class="table">
            <tr>
                <th>Nama Alat / Barang</th>
                <td><strong>{{ strtoupper($penghapusan->asset->nama_alat) }}</strong></td>
            </tr>
            <tr>
                <th>Kode Register / ASPAK</th>
                <td>{{ $penghapusan->asset->no_register ?? '-' }} / {{ $penghapusan->asset->kode_aspak ?? '-' }}</td>
            </tr>
            <tr>
                <th>Lokasi Ruangan</th>
                <td>[{{ $penghapusan->asset->ruangan->kode_ruangan }}] {{ $penghapusan->asset->ruangan->nama_ruangan }}</td>
            </tr>
            <tr>
                <th>Merk / Tipe / No. Seri</th>
                <td>{{ $penghapusan->asset->merk ?? '-' }} / {{ $penghapusan->asset->tipe ?? '-' }} / {{ $penghapusan->asset->no_seri ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tahun Perolehan</th>
                <td>{{ $penghapusan->asset->tahun_perolehan }}</td>
            </tr>
            <tr>
                <th>Nilai Buku / Perolehan</th>
                <td>Rp {{ number_format($penghapusan->asset->harga_perolehan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Alasan Penghapusan</th>
                <td style="color: red; font-weight: bold;">{{ strtoupper($penghapusan->alasan) }}</td>
            </tr>
            <tr>
                <th>Metode & Keterangan</th>
                <td>{{ $penghapusan->metode }} - {{ $penghapusan->keterangan ?? 'Tanpa keterangan tambahan' }}</td>
            </tr>
        </table>

        <p>Proses penghapusan ini dilakukan sesuai dengan ketentuan manajemen aset yang berlaku dan kondisi barang sudah tidak layak guna/tidak ekonomis untuk diperbaiki. Demikian berita acara ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="footer">
        <div class="signature-wrapper">
            <div class="sig-box left">
                <p>Mengetahui,<br>Kepala {{ \App\Models\Setting::getValue('nama_puskesmas') }}</p>
                <div class="spacer"></div>
                <p><strong><u>{{ \App\Models\Setting::getValue('nama_kapus') ?? '( ____________________ )' }}</u></strong><br>
                NIP. {{ \App\Models\Setting::getValue('nip_kapus') ?? '............................' }}</p>
            </div>

            <div class="sig-box right">
                <p>Pekalongan, {{ \Carbon\Carbon::parse($penghapusan->tgl_penghapusan)->translatedFormat('d F Y') }}<br>Pengurus Barang / Petugas,</p>
                <div class="spacer"></div>
                <p><strong><u>{{ \App\Models\Setting::getValue('nama_pengurus_barang') ?? auth()->user()->name }}</u></strong><br>
                NIP. {{ \App\Models\Setting::getValue('nip_pengurus_barang') ?? '............................' }}</p>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>


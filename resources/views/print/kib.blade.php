<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 1cm; size: a4 landscape; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 8pt; line-height: 1.3; color: #000; }
        
        table { width: 100%; border-collapse: collapse; page-break-inside: auto; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; border: 1px solid #000; padding: 5px; font-size: 7pt; }
        td { border: 1px solid #000; padding: 4px; vertical-align: top; }
        
        /* Mencegah baris terpotong di tengah halaman */
        tr { page-break-inside: avoid; page-break-after: auto; }
        
        .footer { margin-top: 30px; float: right; width: 300px; text-align: center; }
        .spacer { height: 60px; }
    </style>
</head>
<body>
    @include('components.kop-surat', ['compact' => true])

    <table>
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="12%">KODE BARANG / REG</th>
                <th width="20%">NAMA BARANG</th>
                <th width="15%">MERK / TYPE</th>
                <th width="12%">NO. SERI / SERTIFIKAT</th>
                <th width="8%">THN PEROLEHAN</th>
                <th width="10%">ASAL USUL</th>
                <th width="10%">KONDISI</th>
                <th width="10%">HARGA (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td align="center">{{ $asset->no_register ?? $asset->kode_aspak }}</td>
                <td>{{ $asset->nama_alat }}</td>
                <td>{{ $asset->merk }} {{ $asset->tipe }}</td>
                <td align="center">{{ $asset->no_seri ?? $asset->no_sertifikat ?? '-' }}</td>
                <td align="center">{{ $asset->tahun_perolehan }}</td>
                <td align="center">{{ $asset->asal_usul ?? 'APBD' }}</td>
                <td align="center">{{ strtoupper($asset->kondisi) }}</td>
                <td align="right">{{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Pekalongan, {{ $tanggal }}</p>
        <p>Kepala Puskesmas,</p>
        <div class="spacer"></div>
        <p><strong><u>{{ \App\Models\Setting::getValue('nama_kepala_puskesmas', '...........................') }}</u></strong></p>
        <p>NIP. {{ \App\Models\Setting::getValue('nip_kepala_puskesmas', '...........................') }}</p>
    </div>
</body>
</html>

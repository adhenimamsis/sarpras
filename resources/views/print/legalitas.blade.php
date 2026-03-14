<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Arial', sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 10px; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    @include('components.kop-surat')

    <table>
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="25%">NAMA ASET</th>
                <th width="20%">JENIS DOKUMEN</th>
                <th width="30%">NOMOR LEGALITAS</th>
                <th width="20%">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>{{ $asset->nama_alat }}</td>
                <td align="center">
                    {{ $asset->kategori_kib == 'KIB_A' ? 'Sertifikat Tanah' : 'IMB / PBG / SLF' }}
                </td>
                <td align="center"><strong>{{ $asset->no_sertifikat ?? $asset->no_pbg_slf ?? '-' }}</strong></td>
                <td>{{ $asset->alamat ?? 'Lokasi Puskesmas' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

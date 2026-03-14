<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Kerusakan Aset - {{ $config['nama_puskesmas'] }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; margin: 0; padding: 20px; }
        
        .title { text-align: center; text-decoration: underline; font-weight: bold; font-size: 14px; margin-bottom: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        th { background-color: #f2f2f2; text-transform: uppercase; font-size: 11px; }

        .footer { margin-top: 30px; width: 100%; }
        .ttd { float: right; width: 250px; text-align: center; }
        .spacer { height: 60px; }
        
        @media print {
            .no-print { display: none; }
            @page { size: landscape; margin: 1cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Laporan</button>
    </div>

    @include('components.kop-surat', ['compact' => true])

    <div class="title">REKAPITULASI KERUSAKAN ASET</div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Tgl Lapor</th>
                <th>Nama Alat / Aset</th>
                <th>Lokasi Ruangan</th>
                <th>Deskripsi Kerusakan</th>
                <th>Tindakan / Solusi</th>
                <th width="80">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $item)
            <tr>
                <td style="text-align: center;">{{ $key + 1 }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->tgl_lapor)->format('d/m/Y') }}</td>
                <td>
                    <strong>{{ $item->asset->nama_alat }}</strong><br>
                    <small>SN: {{ $item->asset->no_seri ?? '-' }}</small>
                </td>
                <td>{{ $item->asset->ruangan->nama_ruangan ?? '-' }}</td>
                <td>{{ $item->deskripsi_kerusakan }}</td>
                <td>{{ $item->tindakan_perbaikan ?? 'Proses Pengecekan' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ strtoupper($item->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd">
            <p>Pekalongan, {{ $config['tgl_cetak'] }}</p>
            <p>Mengetahui,</p>
            <p>Kepala {{ $config['nama_puskesmas'] }}</p>
            <div class="spacer"></div>
            <p><strong><u>{{ $config['kepala_puskesmas'] ?? '( __________________________ )' }}</u></strong></p>
            <p>NIP. {{ $config['nip_kapus'] ?? '........................................' }}</p>
        </div>
    </div>

</body>
</html>

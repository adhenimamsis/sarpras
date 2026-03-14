<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kerusakan Sarpras - {{ \App\Models\Setting::getValue('nama_puskesmas') }}</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12px; 
            line-height: 1.4;
            padding: 10px;
            color: #000;
        }

        .title-laporan { text-align: center; margin-bottom: 20px; }
        .title-laporan h3 { text-decoration: underline; margin: 0; text-transform: uppercase; font-size: 14px; font-weight: bold; }

        /* Tabel Laporan */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 11px; vertical-align: top; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; text-transform: uppercase; }
        
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Tanda Tangan */
        .footer-container { margin-top: 30px; width: 100%; page-break-inside: avoid; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .spacer { height: 70px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            @page { size: A4 landscape; margin: 1.5cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; padding: 15px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px;">
        <h4 style="margin:0;">Pratinjau Cetak Laporan</h4>
        <p style="font-size: 12px; margin: 5px 0 10px 0;">Klik tombol di bawah jika dialog print tidak muncul otomatis. Gunakan orientasi <b>Landscape</b>.</p>
        <button onclick="window.print()" style="cursor:pointer; padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; font-weight: bold;">Cetak Laporan</button>
    </div>

    @include('components.kop-surat', ['compact' => true])

    <div class="title-laporan">
        <h3>REKAPITULASI KERUSAKAN & PERBAIKAN SARPRAS</h3>
        <p style="margin: 5px 0; font-weight: bold;">Periode: {{ request('month', date('F')) }} {{ request('year', date('Y')) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">Tgl Lapor</th>
                <th style="width: 150px;">Nama Alat / Aset</th>
                <th style="width: 100px;">Ruangan</th>
                <th>Deskripsi Kerusakan</th>
                <th>Tindakan / Solusi</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl_lapor)->format('d/m/Y') }}</td>
                <td>
                    <span class="font-bold">{{ $item->asset->nama_alat }}</span><br>
                    <small>Reg: {{ $item->asset->no_register ?? '-' }}</small>
                </td>
                <td class="text-center">
                    {{ $item->asset->ruangan->nama_ruangan ?? '-' }}<br>
                    <small>({{ $item->asset->ruangan->kode_ruangan ?? '-' }})</small>
                </td>
                <td>{{ $item->deskripsi_kerusakan }}</td>
                <td>{{ $item->tindakan_perbaikan ?? 'Proses Pengecekan' }}</td>
                <td class="text-center font-bold">
                    {{ strtoupper($item->status) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada laporan kerusakan untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-container">
        <div class="ttd-box">
            <p>Pekalongan, {{ date('d F Y') }}</p>
            <p>Mengetahui,</p>
            <p style="margin-top: 0;">Pengurus Barang / Jaga Sarpras</p>
            <div class="spacer"></div>
            <p><strong>{{ \App\Models\Setting::getValue('nama_pengurus_barang') ?? '( __________________________ )' }}</strong></p>
            <p style="margin-top: -10px;">NIP. {{ \App\Models\Setting::getValue('nip_pengurus_barang') ?? '........................................' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>


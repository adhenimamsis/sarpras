<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Perbaikan - {{ $record->asset->nama_alat }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 13px; line-height: 1.5; padding: 30px; }

        .title { text-align: center; font-weight: bold; font-size: 15px; margin-bottom: 25px; text-decoration: underline; }

        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 8px; vertical-align: top; border: 1px solid #ddd; }
        .label { background-color: #f9f9f9; font-weight: bold; width: 30%; }

        .work-box { border: 1px solid #000; min-height: 200px; padding: 15px; margin-top: 10px; }
        .work-title { font-weight: bold; margin-bottom: 10px; text-transform: uppercase; border-bottom: 1px solid #eee; }

        .footer { margin-top: 50px; }
        .ttd-container { display: flex; justify-content: space-between; }
        .ttd-box { text-align: center; width: 200px; }
        .spacer { height: 70px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Cetak Form</button>
    </div>

    @include('components.kop-surat')

    <div class="title">FORMULIR PERBAIKAN ALAT</div>

    <table class="info-table">
        <tr>
            <td class="label">Nomor Laporan</td>
            <td>#LP-{{ str_pad($record->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Lapor</td>
            <td>{{ \Carbon\Carbon::parse($record->tgl_lapor)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Nama Alat / Aset</td>
            <td><strong>{{ $record->asset->nama_alat }}</strong> (SN: {{ $record->asset->no_seri ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label">Lokasi Ruangan</td>
            <td>{{ $record->asset->ruangan->nama_ruangan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Pelapor</td>
            <td>{{ $record->pelapor ?? 'Staff Puskesmas' }}</td>
        </tr>
        <tr>
            <td class="label">Deskripsi Kerusakan</td>
            <td style="color: #d9534f; font-weight: bold;">{{ $record->deskripsi_kerusakan }}</td>
        </tr>
    </table>

    <div class="work-box">
        <div class="work-title">Catatan Tindakan Teknisi:</div>
        <p style="color: #999; font-style: italic;">(Diisi oleh teknisi saat pengerjaan lapangan)</p>
        @if($record->tindakan_perbaikan)
            <p>{{ $record->tindakan_perbaikan }}</p>
        @endif
    </div>

    <div class="footer">
        <div class="ttd-container">
            <div class="ttd-box">
                <p>Pelapor/User,</p>
                <div class="spacer"></div>
                <p>( ____________________ )</p>
            </div>
            <div class="ttd-box">
                <p>Pekalongan, {{ $tgl_cetak }}<br>Teknisi / Penanggung Jawab,</p>
                <div class="spacer"></div>
                <p>( ____________________ )</p>
            </div>
        </div>
    </div>

</body>
</html>


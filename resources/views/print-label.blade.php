<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Aset - {{ $asset->nama_alat }}</title>
    <style>
        /* Standar Printer Thermal Stiker 80mm x 40mm */
        @page { 
            size: 80mm 40mm; 
            margin: 0; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; 
            padding: 2mm;
            background-color: #fff;
            color: #000;
        }

        .label-border {
            border: 1.5px solid #000;
            border-radius: 3px;
            height: 36mm; /* Sisa margin printer */
            display: flex;
            align-items: center;
            padding: 2mm;
            box-sizing: border-box;
            position: relative;
        }

        /* Sisi Kiri: QR Code */
        .qr-side {
            flex: 0 0 28mm;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-side svg {
            width: 26mm;
            height: 26mm;
        }

        /* Sisi Kanan: Detail Aset */
        .info-side {
            flex: 1;
            padding-left: 3mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .header {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            margin-bottom: 2px;
            padding-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .details {
            font-size: 8.5px;
            line-height: 1.2;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
        }

        .details td {
            vertical-align: top;
            padding-bottom: 1px;
        }

        .asset-name {
            font-weight: bold;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 20px; /* Batasi tinggi nama alat */
        }

        .footer-text {
            margin-top: auto;
            font-size: 7px;
            font-style: italic;
            border-top: 0.5px dashed #444;
            padding-top: 2px;
        }

        /* Print Settings */
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="label-border">
        <div class="qr-side">
            {!! QrCode::size(100)
                ->style('square')
                ->eye('square')
                ->margin(0)
                ->generate(route('asset.public', $asset->id)) !!}
        </div>

        <div class="info-side">
            <div class="header">
                {{ \App\Models\Setting::getValue('nama_puskesmas') ?? 'INVENTARIS PUSKESMAS' }}
            </div>

            <div class="details">
                <table>
                    <tr>
                        <td width="25%">ID</td>
                        <td>: <strong>{{ $asset->kode_aspak ?: ($asset->no_register ?: $asset->id) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td class="asset-name">: {{ strtoupper($asset->nama_alat) }}</td>
                    </tr>
                    <tr>
                        <td>Ruang</td>
                        <td>: {{ $asset->ruangan->nama_ruangan ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Tahun</td>
                        <td>: {{ $asset->tahun_perolehan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="footer-text">
                SimSarpras - Scan untuk Riwayat & MFK
            </div>
        </div>
    </div>
</body>
</html>
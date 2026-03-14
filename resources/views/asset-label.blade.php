<!DOCTYPE html>
<html>
<head>
    <title>Label Asset - {{ $asset->nama_alat }}</title>
    <style>
        /* Ukuran Stiker Label 50mm x 30mm */
        @page { 
            size: 50mm 30mm; 
            margin: 0; 
        }
        
        body { 
            font-family: 'Helvetica', sans-serif; 
            margin: 0; 
            padding: 2mm;
            width: 50mm;
            height: 30mm;
            box-sizing: border-box;
        }

        .border-container {
            border: 1px solid #000;
            height: 25.5mm; /* Menyesuaikan agar tidak terpotong margin */
            width: 100%;
            padding: 1mm;
            overflow: hidden;
        }

        /* Menggunakan table karena DomPDF belum support Flexbox dengan sempurna */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
        }

        .qr-column {
            width: 18mm;
            vertical-align: middle;
            text-align: center;
        }

        .info-column {
            padding-left: 2mm;
            vertical-align: top;
        }

        .title { 
            font-weight: bold; 
            font-size: 8px; 
            border-bottom: 0.5px solid #000; 
            margin-bottom: 2px;
            text-align: center;
            text-transform: uppercase;
        }

        .info-text { 
            font-size: 7px; 
            line-height: 1.1; 
        }

        .label-footer {
            font-size: 5px;
            text-align: right;
            margin-top: 1mm;
            font-style: italic;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="border-container">
        <div class="title">Puskesmas KITA</div>
        
        <table class="layout-table">
            <tr>
                <td class="qr-column">
                    {{-- QR Code mengarah ke Detail Aset --}}
                    {!! QrCode::size(58)->margin(0)->generate(url('/admin/assets/' . $asset->id)) !!}
                </td>
                <td class="info-column">
                    <div class="info-text">
                        <strong>ID:</strong> {{ $asset->kode_aspak ?? $asset->id }}<br>
                        <strong>Nama:</strong> {{ Str::limit($asset->nama_alat, 25) }}<br>
                        <strong>Ruang:</strong> {{ $asset->ruangan->nama_ruangan ?? '-' }}<br>
                        <strong>Status:</strong> {{ ucfirst($asset->kondisi ?? $asset->status ?? '-') }}
                    </div>
                </td>
            </tr>
        </table>
        
        <div class="label-footer">
            *Scan untuk detail alat
        </div>
    </div>
</body>
</html>
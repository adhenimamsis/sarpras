<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Massal Label QR - {{ config('app.name') }}</title>
    <style>
        /* Reset & Base Style */
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; 
            padding: 10px; 
            background-color: #f4f4f4;
        }

        /* Layout Grid 3 Kolom - Dioptimalkan untuk A4 (210mm x 297mm) */
        .grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 8px; 
            max-width: 200mm; 
            margin: auto;
        }

        /* Card Label Style */
        .card { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: center; 
            border-radius: 6px;
            background: white;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 190px;
            position: relative;
            overflow: hidden;
        }

        /* Header Label */
        .header-label {
            font-size: 8px;
            font-weight: bold;
            color: #1a5a33; /* Hijau Khas Puskesmas */
            text-transform: uppercase;
            width: 100%;
            padding-bottom: 4px;
            border-bottom: 1.5px solid #2ecc71;
            margin-bottom: 6px;
        }

        .qr-container {
            margin: 4px 0;
            padding: 4px;
            border: 1px solid #f0f0f0;
            border-radius: 4px;
        }

        .title { 
            font-size: 10px; 
            font-weight: 800; 
            margin: 4px 0 2px 0;
            line-height: 1.2;
            color: #2c3e50;
            text-transform: uppercase;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 24px;
        }

        .location {
            font-size: 8.5px;
            color: #2980b9;
            font-weight: bold;
            margin-top: 2px;
        }

        .code { 
            font-size: 8px; 
            color: #333; 
            font-family: 'Courier New', monospace;
            background: #ebf5fb;
            padding: 2px 6px;
            border-radius: 10px;
            margin: 4px 0;
            border: 0.5px solid #aed6f1;
        }

        .footer-label {
            font-size: 7px;
            color: #95a5a6;
            margin-top: 4px;
            border-top: 1px solid #f0f0f0;
            width: 100%;
            padding-top: 3px;
            font-style: italic;
        }

        /* Print Settings */
        @media print { 
            body { background-color: transparent; padding: 0; margin: 0; }
            .grid { gap: 4px; width: 100%; }
            .card { border: 1px solid #ccc; box-shadow: none; } 
            .no-print { display: none; }
            @page { size: A4; margin: 1cm; }
        }

        /* UI On Screen */
        .no-print {
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 2px solid #3498db;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn-print {
            background: #27ae60;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .btn-print:hover { background: #219150; }
    </style>
</head>
<body onload="autoPrint()">

    <div class="no-print">
        <h3 style="margin:0 0 5px 0;">🖨️ Siap Mencetak Label</h3>
        <p style="margin:0 0 10px 0; font-size: 14px; color: #444;">
            Ditemukan <b>{{ $assets->count() }} Item</b>. Pastikan printer menggunakan kertas <b>A4</b>.
        </p>
        <button onclick="window.print()" class="btn-print">Klik untuk Cetak</button>
    </div>

    <div class="grid">
        @foreach($assets as $asset)
        <div class="card">
            <div class="header-label">
                {{ \App\Models\Setting::getValue('nama_puskesmas') ?? 'INVENTARIS PUSKESMAS' }}
            </div>
            
            <div class="qr-container">
                {{-- QR Code mengarah ke Public Page Aset untuk Scan Cepat --}}
                {!! QrCode::size(80)
                    ->style('round')
                    ->eye('square')
                    ->color(0, 0, 0)
                    ->generate(route('asset.public', $asset->id)) !!}
            </div>

            <div class="title">{{ $asset->nama_alat }}</div>
            
            <div class="location">
                📍 {{ $asset->ruangan->nama_ruangan ?? 'LOKASI N/A' }}
            </div>

            <div class="code">
                {{ $asset->kode_aspak ?: ($asset->no_register ?: $asset->id) }}
            </div>

            <div class="footer-label">Scan untuk Riwayat & Laporan Kerusakan</div>
        </div>
        @endforeach
    </div>

    <script>
        function autoPrint() {
            // Memberikan waktu browser untuk render SVG QR Code
            setTimeout(() => {
                window.print();
            }, 1000);
        }

        // Kembali ke dashboard setelah print (opsional)
        window.onafterprint = function() {
            // window.history.back(); // Aktifkan jika ingin kembali otomatis
        };
    </script>

</body>
</html>
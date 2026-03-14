<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Label Aset Massal</title>
    <style>
        @page { size: A4; margin: 10mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 10px; }

        .no-print { margin-bottom: 14px; }
        .no-print button {
            border: 0;
            border-radius: 6px;
            padding: 10px 14px;
            background: #059669;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .label-box {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px;
            min-height: 130px;
            page-break-inside: avoid;
            display: grid;
            grid-template-columns: 1fr 70px;
            gap: 8px;
            align-items: center;
        }

        .title { font-size: 9px; font-weight: 700; text-transform: uppercase; color: #0f766e; }
        .asset-name { font-size: 11px; margin-top: 4px; font-weight: 700; line-height: 1.3; }
        .asset-code { font-size: 9px; color: #475569; margin-top: 2px; }
        .qr-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak Semua Label</button>
        <p><small>Gunakan kertas stiker A4 untuk hasil terbaik.</small></p>
    </div>

    <div class="grid-container">
        @foreach($assets as $asset)
            <div class="label-box">
                <div>
                    <div class="title">Inventaris {{ \App\Models\Setting::getValue('nama_puskesmas', 'Puskesmas Bendan') }}</div>
                    <div class="asset-name">{{ \Illuminate\Support\Str::limit($asset->nama_alat, 34) }}</div>
                    <div class="asset-code">ASPAK: {{ $asset->kode_aspak ?? '-' }}</div>
                    <div class="asset-code">Register: {{ $asset->no_register ?? '-' }}</div>
                    <div class="asset-code">Ruang: {{ $asset->ruangan->nama_ruangan ?? '-' }}</div>
                </div>
                <div class="qr-box">
                    {!! QrCode::size(64)->margin(0)->generate(route('asset.public.show', $asset->id)) !!}
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>

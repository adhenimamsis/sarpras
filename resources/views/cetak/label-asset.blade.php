<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Aset - {{ $asset->nama_alat }}</title>
    <style>
        @page { size: 80mm 40mm; margin: 2mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }

        .actions {
            margin: 12px;
            display: flex;
            gap: 8px;
        }
        .actions button {
            border: 0;
            border-radius: 6px;
            padding: 8px 12px;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
        }
        .btn-back { background: #334155; }
        .btn-print { background: #059669; }

        .label-box {
            width: 76mm;
            min-height: 34mm;
            margin: 0 auto;
            border: 1.5px solid #111827;
            border-radius: 4px;
            padding: 4mm;
            display: grid;
            grid-template-columns: 1fr 22mm;
            gap: 3mm;
        }
        .title {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #0f766e;
            font-weight: 700;
            margin-bottom: 2mm;
        }
        .name {
            font-size: 11px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5mm;
        }
        .meta {
            font-size: 8px;
            line-height: 1.4;
        }
        .qr {
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2mm;
            background: #fff;
        }

        @media print {
            .actions { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="actions">
        <button class="btn-back" onclick="window.history.back()">Kembali</button>
        <button class="btn-print" onclick="window.print()">Cetak Label</button>
    </div>

    <div class="label-box">
        <div>
            <div class="title">Inventaris {{ \App\Models\Setting::getValue('nama_puskesmas', 'Puskesmas Bendan') }}</div>
            <div class="name">{{ \Illuminate\Support\Str::limit($asset->nama_alat, 38) }}</div>
            <div class="meta">Kode ASPAK: {{ $asset->kode_aspak ?? '-' }}</div>
            <div class="meta">No Register: {{ $asset->no_register ?? '-' }}</div>
            <div class="meta">Ruangan: {{ $asset->ruangan->nama_ruangan ?? '-' }}</div>
        </div>
        <div class="qr">
            {!! QrCode::size(64)->margin(0)->generate(route('asset.public.show', $asset->id)) !!}
        </div>
    </div>
</body>
</html>

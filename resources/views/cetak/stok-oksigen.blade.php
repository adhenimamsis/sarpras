<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            margin: 24px;
        }
        .header {
            margin-bottom: 16px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 4px 0;
        }
        .meta {
            color: #4b5563;
            margin: 0;
        }
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0 20px 0;
        }
        .summary td {
            border: 1px solid #d1d5db;
            padding: 10px;
        }
        .summary .label {
            width: 70%;
            background: #f9fafb;
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        .data th,
        .data td {
            border: 1px solid #d1d5db;
            padding: 7px;
            vertical-align: top;
        }
        .data th {
            background: #f3f4f6;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    @include('components.kop-surat')

    <div class="header">
        <p class="title">{{ $title }}</p>
        <p class="meta">Dicetak: {{ $tanggal }}</p>
    </div>

    <table class="summary">
        <tr>
            <td class="label">Stok Terakhir Tabung 6m3</td>
            <td class="text-right">{{ $stok_terakhir_besar }} tabung</td>
        </tr>
        <tr>
            <td class="label">Stok Terakhir Tabung 1m3</td>
            <td class="text-right">{{ $stok_terakhir_kecil }} tabung</td>
        </tr>
        <tr>
            <td class="label">Total Riwayat Mutasi</td>
            <td class="text-right">{{ $riwayat->count() }} transaksi</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 42px;">No</th>
                <th style="width: 90px;">Tanggal</th>
                <th>Lokasi</th>
                <th style="width: 56px;">Ukuran</th>
                <th style="width: 60px;" class="text-right">Masuk</th>
                <th style="width: 60px;" class="text-right">Keluar</th>
                <th style="width: 70px;" class="text-right">Stok Akhir</th>
                <th style="width: 90px;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayat as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ optional($item->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $item->lokasi }}</td>
                    <td>{{ $item->ukuran }}</td>
                    <td class="text-right">{{ $item->jumlah_masuk }}</td>
                    <td class="text-right">{{ $item->jumlah_keluar }}</td>
                    <td class="text-right">{{ $item->stok_akhir }}</td>
                    <td>{{ $item->petugas }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Belum ada data mutasi stok oksigen.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>


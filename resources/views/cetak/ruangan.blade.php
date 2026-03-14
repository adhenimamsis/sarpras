<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIR - {{ $ruangan->nama_ruangan }}</title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; line-height: 1.35; margin: 0; background: #f5f7fb; }

        .no-print { display: flex; gap: 8px; justify-content: center; margin: 18px 0; }
        .no-print button {
            border: 0; border-radius: 6px; padding: 10px 14px; cursor: pointer;
            font-size: 12px; font-weight: 700; text-transform: uppercase;
        }
        .btn-back { background: #334155; color: #fff; }
        .btn-print { background: #059669; color: #fff; }

        .page {
            width: 190mm;
            margin: 0 auto 16px;
            background: #fff;
            border: 1px solid #d1d5db;
            padding: 10mm;
            min-height: 267mm;
        }

        .judul { text-align: center; margin-bottom: 10px; }
        .judul h3 { margin: 0; font-size: 14px; text-decoration: underline; }
        .judul p { margin: 3px 0 0; font-size: 11px; font-weight: 700; text-transform: uppercase; }

        .info { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .info td { padding: 2px 0; }
        .info .label { width: 120px; font-weight: 700; }
        .info .sep { width: 14px; text-align: center; }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th,
        .data-table td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        .data-table th { background: #f3f4f6; text-transform: uppercase; font-size: 10px; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .kondisi-baik { color: #166534; font-weight: 700; }
        .kondisi-ringan { color: #a16207; font-weight: 700; }
        .kondisi-berat { color: #991b1b; font-weight: 700; }

        .ttd { width: 100%; margin-top: 22px; border-collapse: collapse; }
        .ttd td { width: 50%; text-align: center; vertical-align: top; }
        .spacer { height: 64px; }

        .footer-note {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .page { width: auto; margin: 0; border: 0; padding: 0; min-height: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-back" onclick="window.history.back()">Kembali</button>
        <button class="btn-print" onclick="window.print()">Cetak KIR</button>
    </div>

    <div class="page">
        @include('components.kop-surat')

        <div class="judul">
            <h3>Kartu Inventaris Ruangan (KIR)</h3>
            <p>Ruangan: {{ $ruangan->nama_ruangan }}</p>
        </div>

        <table class="info">
            <tr>
                <td class="label">Kode Ruangan</td>
                <td class="sep">:</td>
                <td style="width: 35%;">{{ $ruangan->kode_ruangan ?? '-' }}</td>
                <td class="label">Tahun</td>
                <td class="sep">:</td>
                <td>{{ date('Y') }}</td>
            </tr>
            <tr>
                <td class="label">Gedung / Lantai</td>
                <td class="sep">:</td>
                <td>{{ $ruangan->gedung ?? '-' }} / {{ $ruangan->lantai ?? '-' }}</td>
                <td class="label">Unit Kerja</td>
                <td class="sep">:</td>
                <td>{{ \App\Models\Setting::getValue('nama_puskesmas', 'UPT Puskesmas Bendan') }}</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 18%;">Kode ASPAK / Register</th>
                    <th style="width: 35%;">Nama Barang / Merk</th>
                    <th style="width: 12%;">Kondisi</th>
                    <th style="width: 10%;">Jumlah</th>
                    <th style="width: 21%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ruangan->assets as $index => $asset)
                    @php
                        $kondisiLower = strtolower((string) $asset->kondisi);
                        $kondisiClass = str_contains($kondisiLower, 'berat') || $kondisiLower === 'rb'
                            ? 'kondisi-berat'
                            : (str_contains($kondisiLower, 'ringan') || in_array($kondisiLower, ['rr', 'kb'], true)
                                ? 'kondisi-ringan'
                                : 'kondisi-baik');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $asset->kode_aspak ?? '-' }}<br>{{ $asset->no_register ?? '-' }}</td>
                        <td>
                            <strong>{{ $asset->nama_alat }}</strong><br>
                            <span style="font-size: 9px; color: #4b5563;">{{ trim(($asset->merk ?? '').' '.($asset->tipe ?? '')) ?: '-' }}</span>
                        </td>
                        <td class="text-center"><span class="{{ $kondisiClass }}">{{ $asset->kondisi ?? '-' }}</span></td>
                        <td class="text-center">1 Unit</td>
                        <td>{{ $asset->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 14px; color: #6b7280;">Belum ada data aset pada ruangan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="ttd">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala {{ \App\Models\Setting::getValue('nama_puskesmas', 'UPT Puskesmas Bendan') }}</strong>
                    <div class="spacer"></div>
                    <strong><u>{{ \App\Models\Setting::getValue('nama_kapus', '(Nama Kepala Puskesmas)') }}</u></strong><br>
                    NIP. {{ \App\Models\Setting::getValue('nip_kapus', '........................................') }}
                </td>
                <td>
                    Pekalongan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Pengurus Barang</strong>
                    <div class="spacer"></div>
                    <strong><u>{{ auth()->user()->name }}</u></strong><br>
                    NIP. {{ \App\Models\Setting::getValue('nip_pengurus_barang', '........................................') }}
                </td>
            </tr>
        </table>

        <div class="footer-note">
            <span>KIR Digital - SIM Sarpras</span>
            <span>Waktu Cetak: {{ date('d/m/Y H:i') }}</span>
        </div>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Monitoring MFK - {{ $record->jenis_utilitas }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; line-height: 1.5; color: #000; margin: 0; padding: 20px; }
        
        .judul-doc { text-align: center; text-decoration: underline; font-weight: bold; font-size: 14px; margin-bottom: 20px; text-transform: uppercase; }

        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 4px; vertical-align: top; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .main-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; text-transform: uppercase; }

        .status-badge { font-weight: bold; text-transform: uppercase; padding: 2px 5px; border: 1px solid #000; }
        
        .footer { margin-top: 30px; width: 100%; page-break-inside: avoid; }
        .footer td { text-align: center; width: 50%; vertical-align: top; }
        .signature-space { height: 70px; }
        
        .photo-box { margin-top: 20px; text-align: center; page-break-inside: avoid; }
        .photo-box img { max-width: 350px; border: 1px solid #000; padding: 2px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.history.back()" style="padding: 10px 15px; cursor: pointer; background: #6b7280; color: white; border: none; border-radius: 4px; margin-right: 5px;">
            Kembali
        </button>
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #1e40af; color: white; border: none; border-radius: 4px; font-weight: bold;">
            Cetak Laporan MFK
        </button>
    </div>

    @include('components.kop-surat')

    <div class="judul-doc">LAPORAN MONITORING UTILITAS & MFK</div>

    <table class="info-table">
        <tr>
            <td width="20%">Objek Pemeriksaan</td>
            <td width="2%">:</td>
            <td width="28%"><strong>{{ strtoupper($record->jenis_utilitas) }}</strong></td>
            <td width="20%">ID Log</td>
            <td width="2%">:</td>
            <td width="28%">#{{ $record->id }}</td>
        </tr>
        <tr>
            <td>Tanggal Cek</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($record->tgl_cek)->translatedFormat('d F Y') }}</td>
            <td>Waktu Cek</td>
            <td>:</td>
            <td>{{ $record->waktu_cek }} WIB</td>
        </tr>
        <tr>
            <td>Petugas Pemeriksa</td>
            <td>:</td>
            <td colspan="4">{{ $record->petugas }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="40%">Parameter Pemeriksaan</th>
                <th width="55%">Hasil Temuan / Detail Kondisi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Status Kondisi Akhir</td>
                <td>
                    <span class="status-badge">
                        {{ $record->status }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Catatan Teknis Petugas</td>
                <td>{{ $record->parameter_cek }}</td>
            </tr>
            
            {{-- Khusus APAR jika ada detail --}}
            @if($record->jenis_utilitas === 'APAR' && $record->detail_apar)
            <tr>
                <td style="text-align: center;">3</td>
                <td>Detail Per-Unit (Inventory)</td>
                <td>
                    <ul style="margin: 0; padding-left: 15px;">
                        @foreach($record->detail_apar as $apar)
                            <li>{{ $apar['nama_apar'] }}: <strong>{{ strtoupper($apar['kondisi']) }}</strong></li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @endif

            {{-- RTL jika kondisi tidak normal --}}
            @if($record->status !== 'Normal' || $record->keterangan)
            <tr>
                <td style="text-align: center;">4</td>
                <td style="color: red; font-weight: bold;">RENCANA TINDAK LANJUT (RTL)</td>
                <td style="background-color: #fff5f5; border: 1px solid red;">
                    <strong>{{ $record->keterangan ?? 'Segera dilakukan perbaikan/pemeliharaan teknis.' }}</strong>
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    @php
        $fotoBuktiDataUrl = \App\Support\Files\PublicImageDataUrl::fromPath($record->foto_bukti);
    @endphp
    @if($fotoBuktiDataUrl)
    <div class="photo-box">
        <p style="text-decoration: underline; margin-bottom: 10px;"><strong>DOKUMENTASI FOTO LAPANGAN</strong></p>
        {{-- Base64 loading untuk keawetan PDF --}}
        <img src="{{ $fotoBuktiDataUrl }}" alt="Bukti Lapangan">
    </div>
    @endif

    <table class="footer">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala UPT Puskesmas Bendan
                <div class="signature-space"></div>
                <strong>( ............................................ )</strong><br>
                NIP. ........................................
            </td>
            <td>
                Pekalongan, {{ \Carbon\Carbon::parse($record->tgl_cek)->translatedFormat('d F Y') }}<br>
                Petugas Sarpras / MFK
                <div class="signature-space"></div>
                <strong>( {{ $record->petugas }} )</strong><br>
                Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    <div style="margin-top: 50px; text-align: center; font-size: 9px; color: #666; font-style: italic; border-top: 1px solid #ccc; padding-top: 5px;">
        Dokumen ini dihasilkan secara otomatis melalui Sistem Informasi Sarpras Digital Puskesmas Bendan.
    </div>
</body>
</html>


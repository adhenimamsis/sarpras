<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Laporan Komprehensif MFK - Puskesmas Bendan' }}</title>
    <style>
        @page { 
            margin: 1cm 1.5cm 1.5cm 1.5cm; 
            size: A4;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9px; 
            color: #000; 
            line-height: 1.4; 
            margin: 0; 
        }

        .report-title { text-align: center; margin: 20px 0; }
        .report-title h3 { text-decoration: underline; margin: 0; font-size: 12px; text-transform: uppercase; font-weight: bold; }

        .section-title { 
            background: #e9ecef; 
            padding: 6px; 
            font-weight: bold; 
            border: 1px solid #000; 
            margin-top: 15px; 
            text-transform: uppercase; 
            font-size: 10px; 
        }
        
        /* TABLE STYLING STANDAR PEMERINTAH */
        table { width: 100%; border-collapse: collapse; margin-top: 0; table-layout: fixed; }
        table th, table td { border: 1px solid #000; padding: 5px 6px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; text-transform: uppercase; font-size: 8.5px; }
        td { word-wrap: break-word; vertical-align: middle; }
        
        .summary-box { border: 1px solid #000; border-top: none; padding: 10px; background-color: #fff; }
        .summary-table td { border: none; padding: 3px 5px; font-size: 9px; vertical-align: top; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .critical { color: #b91c1c; font-weight: bold; background-color: #fee2e2 !important; }
        
        /* FOOTER TANDA TANGAN */
        .footer-container { margin-top: 40px; page-break-inside: avoid; }
        .sign-wrapper { width: 100%; }
        .sign-left { float: left; width: 45%; text-align: center; }
        .sign-right { float: right; width: 45%; text-align: center; }
        .spacer { height: 75px; }
        .clear { clear: both; }

        .info-keterangan { font-size: 7.5px; font-style: italic; color: #444; margin-top: 10px; }
    </style>
</head>
<body>

    @include('components.kop-surat')

    <div class="report-title">
        <h3>{{ $title ?? 'LAPORAN KOMPREHENSIF FASILITAS DAN KESELAMATAN (MFK)' }}</h3>
        <p style="margin: 5px 0; font-size: 10px;">Periode Pelaporan: <b>{{ $periode }}</b></p>
    </div>

    <div class="section-title">I. RINGKASAN EKSEKUTIF STATUS OPERASIONAL</div>
    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td width="50%">
                    <span class="font-bold">A. Inventaris & Kondisi Aset:</span><br>
                    - Total Aset Terdaftar : {{ $total_asset }} Unit<br>
                    - Kondisi Baik : {{ $asset_baik }} Unit<br>
                    - Kondisi Rusak (R/B) : {{ $asset_rusak }} Unit
                </td>
                <td>
                    <span class="font-bold">B. Kepatuhan Kalibrasi Alkes:</span><br>
                    - Alat Terkalibrasi : {{ $kalibrasi_summary['sudah_kalibrasi'] }} Unit<br>
                    - Jatuh Tempo/Expired : <span class="{{ $kalibrasi_summary['jatuh_tempo'] > 0 ? 'critical' : '' }}">{{ $kalibrasi_summary['jatuh_tempo'] }} Unit</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="font-bold">C. Monitoring Utilitas & Gas Medik:</span><br>
                    - Listrik & Air : {{ $monitoring['listrik'] ?? 'Dalam Kondisi Baik' }}<br>
                    - Stok Oksigen Medis : {{ $stok_oksigen->sum('stok_akhir') }} Tabung (Tersedia)
                </td>
                <td>
                    <span class="font-bold">D. Sistem Proteksi Kebakaran:</span><br>
                    - APAR : {{ $mfk['apar'] ?? 'Tersedia & Berlaku' }}<br>
                    - Jalur Evakuasi : {{ $mfk['evakuasi'] ?? 'Terbuka & Aman' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">II. DETAIL DISTRIBUSI ASET & STATUS PEMELIHARAAN</div>
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">No. Register</th>
                <th width="32%">Nama Sarana / Prasarana / Alat</th>
                <th width="18%">Ruangan / Lokasi</th>
                <th width="10%">Kondisi</th>
                <th width="12%">Nilai Buku</th>
                <th width="12%">Kalibrasi / Maint.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            @php
                $isExpired = $asset->tgl_kalibrasi_selanjutnya && $asset->tgl_kalibrasi_selanjutnya < date('Y-m-d');
            @endphp
            <tr class="{{ $isExpired ? 'critical' : '' }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center font-bold" style="font-size: 8px;">{{ $asset->no_register ?? $asset->kode_aspak ?? '-' }}</td>
                <td>
                    <span class="font-bold">{{ $asset->nama_alat }}</span><br>
                    <small>Merk/Tipe: {{ $asset->merk ?? '-' }} {{ $asset->tipe ?? '' }}</small>
                </td>
                <td>
                    {{ $asset->ruangan->nama_ruangan ?? '-' }}<br>
                    <small><b>ID: {{ $asset->ruangan->kode_ruangan ?? 'N/A' }}</b></small>
                </td>
                <td class="text-center">{{ strtoupper($asset->kondisi) }}</td>
                <td class="text-right">{{ number_format($asset->nilai_buku ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($asset->tgl_kalibrasi_selanjutnya)
                        <span style="font-size: 8px;">{{ date('d/m/Y', strtotime($asset->tgl_kalibrasi_selanjutnya)) }}</span><br>
                        <small>({{ $isExpired ? 'EXPIRED' : 'VALID' }})</small>
                    @else
                        <small>Rutin</small>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="info-keterangan">* Catatan: Baris berwarna merah menunjukkan aset yang telah melewati jatuh tempo kalibrasi atau membutuhkan tindakan segera.</p>

    <div class="footer-container">
        <div class="sign-wrapper">
            <div class="sign-left">
                <p>Mengetahui,</p>
                <p>Pejabat Pengurus Barang / Sarpras</p>
                <div class="spacer"></div>
                <p><strong>( {{ auth()->user()->name }} )</strong></p>
                <p>NIP. ...................................</p>
            </div>

            <div class="sign-right">
                <p>Pekalongan, {{ $tanggal ?? date('d F Y') }}</p>
                <p>Kepala {{ \App\Models\Setting::getValue('nama_puskesmas', 'UPT Puskesmas Bendan') }}</p>
                <div class="spacer"></div>
                <p><strong>( {{ \App\Models\Setting::getValue('nama_kepala_puskesmas', '__________________________') }} )</strong></p>
                <p>NIP. {{ \App\Models\Setting::getValue('nip_kepala_puskesmas', '...................................') }}</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>

</body>
</html>

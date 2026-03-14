<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 1.25cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #000; margin: 0; padding: 0; line-height: 1.4; }

        .judul-laporan { text-align: center; margin-bottom: 25px; }
        .judul-laporan h4 { margin: 0; text-decoration: underline; text-transform: uppercase; font-size: 11pt; font-weight: bold; }
        
        /* Styling Tabel */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        table th, table td { border: 1px solid #000; padding: 5px 4px; font-size: 8.5pt; vertical-align: middle; word-wrap: break-word; }
        table th { background-color: #f2f2f2; text-align: center; text-transform: uppercase; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        
        /* Indikator Kritis */
        .bg-critical { background-color: #f8d7da !important; color: #721c24; }
        .page-break { page-break-before: always; }
        
        /* Footer Tanda Tangan */
        .footer-sign { margin-top: 30px; width: 100%; }
        .sign-box { float: right; width: 250px; text-align: center; }
        .spacer { height: 70px; }
        .clear { clear: both; }

        .info-keterangan { font-size: 8pt; font-style: italic; color: #444; margin-top: -5px; margin-bottom: 20px; }
    </style>
</head>
<body>

    @include('components.kop-surat')

    <div class="judul-laporan">
        <h4>{{ $title }}</h4>
        <p style="font-size: 9pt; margin-top: 5px;">Periode Pelaporan: <b>{{ $periode }}</b></p>
    </div>

    {{-- BAGIAN 1: INVENTARIS --}}
    <h5 style="margin-bottom: 8px; text-transform: uppercase;">A. Inventaris & Status Kalibrasi Aset Medis</h5>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Alat / Merk</th>
                <th width="15%">No. Seri</th>
                <th width="15%">Lokasi Ruang</th>
                <th width="10%">Kondisi</th>
                <th width="12%">Nilai Buku</th>
                <th width="13%">Jatuh Tempo Kalibrasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            @php
                $isExpired = $asset->tgl_kalibrasi_selanjutnya && $asset->tgl_kalibrasi_selanjutnya < date('Y-m-d');
            @endphp
            <tr class="{{ $isExpired ? 'bg-critical' : '' }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <span class="text-bold">{{ $asset->nama_alat }}</span><br>
                    <small>{{ $asset->merk }} {{ $asset->tipe }}</small>
                </td>
                <td class="text-center">{{ $asset->no_seri ?? '-' }}</td>
                <td class="text-center">{{ $asset->ruangan->nama_ruangan ?? '-' }}</td>
                <td class="text-center">{{ strtoupper($asset->kondisi) }}</td>
                <td class="text-right">{{ number_format($asset->nilai_buku ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">
                    {{ $asset->tgl_kalibrasi_selanjutnya ? date('d/m/Y', strtotime($asset->tgl_kalibrasi_selanjutnya)) : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- BAGIAN 2: MFK --}}
    <h5 style="margin-top: 20px; margin-bottom: 8px; text-transform: uppercase;">B. Ringkasan Monitoring Fasilitas (MFK)</h5>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="25%">Item Pemeriksaan</th>
                <th width="15%">Status</th>
                <th width="40%">Hasil Temuan / Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mfk_issues as $index => $mfk)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($mfk->tgl_cek)) }}</td>
                <td class="text-bold">{{ $mfk->jenis_utilitas }}</td>
                <td class="text-center">{{ strtoupper($mfk->status) }}</td>
                <td>{{ $mfk->keterangan ?? 'Pemeliharaan Rutin / Normal' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center italic" style="color: #666;">Tidak ada laporan gangguan fasilitas dalam periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- BAGIAN 3: GAS MEDIK --}}
    <div class="page-break"></div>
    <h5 style="margin-bottom: 8px; text-transform: uppercase;">C. Manajemen Gas Medik & Utilitas</h5>
    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="40%">Deskripsi Logistik</th>
                <th width="25%">Volume / Stok</th>
                <th width="25%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stok_oksigen as $index => $stok)
            @php $isLow = $stok->stok_akhir < 3; @endphp
            <tr class="{{ $isLow ? 'bg-critical' : '' }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>Oksigen Tabung Ukuran <b>{{ $stok->ukuran }}</b></td>
                <td class="text-center">{{ $stok->stok_akhir }} Tabung</td>
                <td class="text-center text-bold">
                    {{ $isLow ? 'STOK KRITIS' : 'Persediaan Aman' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="info-keterangan">
        * Catatan: Baris dengan latar belakang merah muda menandakan aset melewati masa kalibrasi atau stok logistik kritis yang memerlukan tindak lanjut segera.
    </div>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Pekalongan, {{ $tanggal }}</p>
            <p>Mengetahui,</p>
            <p><strong>Kepala {{ \App\Models\Setting::getValue('nama_puskesmas', 'UPT Puskesmas Bendan') }}</strong></p>
            <div class="spacer"></div>
            <p><strong><u>{{ \App\Models\Setting::getValue('nama_kepala_puskesmas', '..........................................') }}</u></strong></p>
            <p>NIP. {{ \App\Models\Setting::getValue('nip_kepala_puskesmas', '..........................................') }}</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Monitoring Terpadu MFK & Utilitas</title>
    <style>
        @page { margin: 1cm; size: A4; }
        body { font-family: 'Arial', sans-serif; font-size: 8.5pt; color: #000; line-height: 1.3; }

        .judul-doc { text-align: center; font-size: 11pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin: 10px 0 2px 0; }
        .nomor-doc { text-align: center; font-size: 8.5pt; margin-bottom: 10px; }
        
        .info-tab { width: 100%; margin-bottom: 10px; border: none !important; }
        .info-tab td { border: none !important; padding: 1px 0; }

        .section-title { background: #f2f2f2; padding: 5px; font-weight: bold; border: 1px solid #000; margin-top: 10px; font-size: 8.5pt; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table, th, td { border: 1px solid #000; }
        th { padding: 6px; background: #f9f9f9; text-transform: uppercase; font-size: 7.5pt; text-align: center; }
        td { padding: 4px; vertical-align: top; }
        
        .footer { margin-top: 20px; width: 100%; }
        .sign-table { width: 100%; border: none !important; }
        .sign-table td { border: none !important; text-align: center; width: 50%; padding-top: 10px; }
        .spacer { height: 50px; }
    </style>
</head>
<body>
    @include('components.kop-surat')

    <div class="judul-doc">CHECKLIST MONITORING UTILITAS & MFK</div>
    <div class="nomor-doc">Nomor: ......... / ......... / ......... / {{ date('Y') }}</div>

    <table class="info-tab">
        <tr>
            <td style="width: 15%;">Hari / Tanggal</td><td style="width: 35%;">: ...................................</td>
            <td style="width: 15%;">Waktu Cek</td><td style="width: 35%;">: ........... WIB</td>
        </tr>
    </table>

    <div class="section-title">I. MONITORING KELISTRIKAN (3 PANEL/METERAN)</div>
    <table>
        <thead>
            <tr>
                <th width="30%">Lokasi Panel / Meteran</th>
                <th width="40%">Data Meteran (kWh)</th>
                <th width="15%">Voltase</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>Meteran 1 (Gedung Induk)</b></td>
                <td>Stand Akhir: ...........................</td>
                <td style="text-align:center;">........... V</td>
                <td style="text-align:center;">Normal / Ggn</td>
            </tr>
            <tr>
                <td><b>Meteran 2 (Gedung Poned/UGD)</b></td>
                <td>Stand Akhir: ...........................</td>
                <td style="text-align:center;">........... V</td>
                <td style="text-align:center;">Normal / Ggn</td>
            </tr>
            <tr>
                <td><b>Meteran 3 (Gedung Rawat Jalan)</b></td>
                <td>Stand Akhir: ...........................</td>
                <td style="text-align:center;">........... V</td>
                <td style="text-align:center;">Normal / Ggn</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">II. MONITORING GENSET (3 UNIT)</div>
    <table>
        <thead>
            <tr>
                <th width="30%">Unit Genset</th>
                <th width="20%">BBM (Solar)</th>
                <th width="25%">Kondisi Aki/Baterai</th>
                <th>Pemanasan (Running)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>Genset 01 (Utama)</b></td>
                <td style="text-align:center;">........... %</td>
                <td style="text-align:center;">Baik / Lemah</td>
                <td>[ ] Ya [ ] Tidak</td>
            </tr>
            <tr>
                <td><b>Genset 02 (Cadangan 1)</b></td>
                <td style="text-align:center;">........... %</td>
                <td style="text-align:center;">Baik / Lemah</td>
                <td>[ ] Ya [ ] Tidak</td>
            </tr>
            <tr>
                <td><b>Genset 03 (Cadangan 2)</b></td>
                <td style="text-align:center;">........... %</td>
                <td style="text-align:center;">Baik / Lemah</td>
                <td>[ ] Ya [ ] Tidak</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">III. AIR BERSIH & KESELAMATAN FASILITAS (MFK)</div>
    <table>
        <thead>
            <tr>
                <th width="30%">Aspek</th>
                <th width="45%">Item Pemeriksaan</th>
                <th width="10%">Kondisi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>Air Bersih</b></td>
                <td>Kondisi Pompa, Kebersihan Toren, Stok Air.</td>
                <td style="text-align:center;">B / R</td>
                <td></td>
            </tr>
            <tr>
                <td><b>Proteksi Kebakaran</b></td>
                <td>Tekanan APAR, Jalur Evakuasi, <i>Fire Alarm</i>.</td>
                <td style="text-align:center;">B / R</td>
                <td></td>
            </tr>
            <tr>
                <td><b>Keamanan & Limbah</b></td>
                <td>CCTV, Penerangan, TPS B3, Kondisi IPAL.</td>
                <td style="text-align:center;">B / R</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <table class="sign-table">
            <tr>
                <td>
                    Mengetahui,<br>Koordinator Sarpras / MFK
                    <div class="spacer"></div>
                    <b><u>{{ \App\Models\Setting::getValue('nama_pj_sarpras', '............................................') }}</u></b><br>
                    NIP. {{ \App\Models\Setting::getValue('nip_pj_sarpras', '............................................') }}
                </td>
                <td>
                    Pekalongan, ............................<br>Petugas Pemeriksa,
                    <div class="spacer"></div>
                    ( ............................................ )<br>
                    NIP / NRK. ............................
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

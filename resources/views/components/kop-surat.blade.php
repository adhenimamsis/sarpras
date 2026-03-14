@php
    $compact = (bool) ($compact ?? false);

    $namaPuskesmas = \App\Models\Setting::getValue('nama_puskesmas', 'UPT PUSKESMAS BENDAN');
    $alamatPuskesmas = \App\Models\Setting::getValue('alamat_puskesmas', 'Jalan Slamet No. 2 Pekalongan Kode Pos 51119');
    $telpPuskesmas = \App\Models\Setting::getValue('telp_puskesmas', '(0285) 421442');
    $emailPuskesmas = \App\Models\Setting::getValue('email_puskesmas', 'uptpuskesmasbendan@gmail.com');
    $websitePuskesmas = \App\Models\Setting::getValue('website_puskesmas', 'https://pkm-bendan.pekalongankota.go.id/');

    $logoPemkotPath = public_path('images/logo-pemkot.png');
    $logoPuskesmasPath = public_path('images/logo-Puskesmas.png');

    $logoPemkot = file_exists($logoPemkotPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPemkotPath))
        : null;

    $logoPuskesmas = file_exists($logoPuskesmasPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPuskesmasPath))
        : null;

    $bottomPadding = $compact ? '6px' : '10px';
    $bottomMargin = $compact ? '14px' : '18px';
    $leftColWidth = $compact ? '64px' : '78px';
    $rightColWidth = $compact ? '58px' : '70px';
    $textColWidth = $compact ? '470px' : '520px';
    $logoLeftWidth = $compact ? '56' : '68';
    $logoRightWidth = $compact ? '48' : '58';
    $fontH3 = $compact ? '13px' : '15px';
    $fontH2 = $compact ? '15px' : '17px';
    $fontH1 = $compact ? '17px' : '19px';
    $fontP = $compact ? '9px' : '10px';
    $lineHeight = $compact ? '1.2' : '1.3';
@endphp

<div style="width: 100%; border-bottom: 3px double #000; padding-bottom: {{ $bottomPadding }}; margin-bottom: {{ $bottomMargin }}; font-family: Arial, sans-serif; text-align: center;">
    <div style="display: inline-block; vertical-align: middle; width: {{ $leftColWidth }}; text-align: center;">
        @if($logoPemkot)
            <img src="{{ $logoPemkot }}" alt="Logo Pemkot" width="{{ $logoLeftWidth }}" style="display: inline-block; height: auto;">
        @endif
    </div>

    <div style="display: inline-block; vertical-align: middle; width: {{ $textColWidth }}; text-align: center; margin: 0 8px;">
        <h3 style="margin: 0; text-transform: uppercase; font-size: {{ $fontH3 }}; font-weight: 700; line-height: 1.2;">
            PEMERINTAH KOTA PEKALONGAN
        </h3>
        <h2 style="margin: 0; text-transform: uppercase; font-size: {{ $fontH2 }}; font-weight: 700; line-height: 1.2;">
            DINAS KESEHATAN
        </h2>
        <h1 style="margin: 0; text-transform: uppercase; font-size: {{ $fontH1 }}; font-weight: 800; line-height: 1.2;">
            {{ strtoupper($namaPuskesmas) }}
        </h1>
        <p style="margin: 4px 0 0 0; font-size: {{ $fontP }}; line-height: {{ $lineHeight }};">
            {{ $alamatPuskesmas }}<br>
            Telp. {{ $telpPuskesmas }} | Pos-el: {{ $emailPuskesmas }}<br>
            {{ $websitePuskesmas }}
        </p>
    </div>

    <div style="display: inline-block; vertical-align: middle; width: {{ $rightColWidth }}; text-align: center;">
        @if($logoPuskesmas)
            <img src="{{ $logoPuskesmas }}" alt="Logo Puskesmas" width="{{ $logoRightWidth }}" style="display: inline-block; height: auto;">
        @endif
    </div>
</div>

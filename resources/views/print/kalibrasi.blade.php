<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 1.5cm; size: a4 portrait; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #eee; text-transform: uppercase; }
        
        .urgent { background-color: #ffe5e5; color: #b91c1c; font-weight: bold; }
    </style>
</head>
<body>
    @include('components.kop-surat')

    <table>
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="35%">NAMA ALAT KESEHATAN</th>
                <th width="20%">LOKASI RUANG</th>
                <th width="20%">TGL TERAKHIR</th>
                <th width="20%">JADWAL BERIKUTNYA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            @php 
                $nextDate = \Carbon\Carbon::parse($asset->tgl_kalibrasi_selanjutnya);
                $isOverdue = $nextDate->isPast();
            @endphp
            <tr class="{{ $isOverdue ? 'urgent' : '' }}">
                <td align="center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $asset->nama_alat }}</strong><br>
                    <small>S/N: {{ $asset->no_seri ?? '-' }}</small>
                </td>
                <td align="center">{{ $asset->ruangan->nama_ruangan ?? '-' }}</td>
                <td align="center">{{ $asset->tgl_kalibrasi_terakhir ? date('d/m/Y', strtotime($asset->tgl_kalibrasi_terakhir)) : '-' }}</td>
                <td align="center">
                    {{ $nextDate->format('d/m/Y') }}
                    @if($isOverdue) <br><small>(SEGERA KALIBRASI)</small> @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

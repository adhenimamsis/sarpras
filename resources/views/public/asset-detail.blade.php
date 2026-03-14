<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Aset - {{ $asset->nama_alat }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-welcome-page min-h-screen antialiased">
    <div class="theme-welcome-canvas fixed inset-0 -z-20"></div>
    @php
        $kondisi = $asset->kondisi ?? 'Tidak Diketahui';
        $statusColor = match (strtolower($kondisi)) {
            'baik', 'b' => 'bg-emerald-500/20 text-emerald-200 border-emerald-300/40',
            'rusak ringan', 'rr', 'kb' => 'bg-amber-500/20 text-amber-200 border-amber-300/40',
            'rusak berat', 'rb' => 'bg-rose-500/20 text-rose-200 border-rose-300/40',
            default => 'bg-slate-700/60 text-slate-200 border-slate-500/50',
        };

        $waAdmin = preg_replace('/[^0-9]/', '', (string) \App\Models\Setting::getValue('wa_admin_notif', ''));
        if (str_starts_with($waAdmin, '0')) {
            $waAdmin = '62'.substr($waAdmin, 1);
        }
    @endphp

    <header class="theme-welcome-soft-panel sticky top-0 z-30 border-b border-slate-700/50 backdrop-blur">
        <div class="mx-auto flex w-full max-w-3xl items-center gap-3 px-4 py-3">
            <img src="{{ asset('images/logo-Puskesmas.png') }}" alt="Logo" class="h-9 w-9 rounded-lg bg-white p-1 shadow">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-300">Scan Aset</p>
                <p class="text-sm font-bold text-slate-100">{{ \App\Models\Setting::getValue('nama_puskesmas', 'UPT Puskesmas Bendan') }}</p>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-3xl space-y-4 px-4 py-5">
        <section class="theme-welcome-panel rounded-2xl p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="text-lg font-black uppercase tracking-tight text-slate-100 sm:text-xl">{{ $asset->nama_alat }}</h1>
                    <p class="mt-1 text-xs text-slate-300">No Register: {{ $asset->no_register ?? '-' }} | Kode ASPAK: {{ $asset->kode_aspak ?? '-' }}</p>
                </div>
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                    {{ strtoupper($kondisi) }}
                </span>
            </div>

            <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <div class="rounded-xl border border-slate-700/50 bg-slate-900/55 p-3">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Lokasi</p>
                    <p class="mt-1 font-semibold text-slate-100">{{ $asset->ruangan->nama_ruangan ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-slate-700/50 bg-slate-900/55 p-3">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Tahun Perolehan</p>
                    <p class="mt-1 font-semibold text-slate-100">{{ $asset->tahun_perolehan ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-slate-700/50 bg-slate-900/55 p-3">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Merk / Tipe</p>
                    <p class="mt-1 font-semibold text-slate-100">{{ $asset->merk ?? '-' }} {{ $asset->tipe ?? '' }}</p>
                </div>
                <div class="rounded-xl border border-slate-700/50 bg-slate-900/55 p-3">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Umur Aset</p>
                    <p class="mt-1 font-semibold text-slate-100">{{ $umur_aset !== null ? $umur_aset.' tahun' : '-' }}</p>
                </div>
            </div>
        </section>

        <section class="theme-welcome-panel rounded-2xl p-5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-[0.16em] text-emerald-200">Riwayat Pemeliharaan Terakhir</h2>
            <div class="mt-3 space-y-2">
                @forelse($asset->maintenanceLogs as $log)
                    <article class="rounded-xl border border-slate-700/50 bg-slate-900/55 p-3">
                        <p class="text-sm font-semibold text-slate-100">{{ $log->jenis_tindakan ?? 'Pemeliharaan' }}</p>
                        <p class="text-xs text-slate-300">{{ $log->tanggal_servis ? \Carbon\Carbon::parse($log->tanggal_servis)->translatedFormat('d F Y') : '-' }} | Teknisi: {{ $log->teknisi ?? '-' }}</p>
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-600 p-3 text-sm text-slate-300">Belum ada riwayat pemeliharaan tercatat.</p>
                @endforelse
            </div>
        </section>

        <section class="theme-welcome-panel rounded-2xl p-5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-[0.16em] text-cyan-200">Laporan Kerusakan Aktif</h2>
            <div class="mt-3 space-y-2">
                @forelse($asset->laporanKerusakans as $laporan)
                    <article class="rounded-xl border border-amber-300/40 bg-amber-500/15 p-3">
                        <p class="text-sm font-semibold text-amber-200">{{ $laporan->status }}</p>
                        <p class="text-xs text-amber-100">{{ $laporan->deskripsi_kerusakan }}</p>
                        <p class="mt-1 text-[11px] text-amber-200/80">Pelapor: {{ $laporan->pelapor }} | {{ $laporan->tgl_lapor ? \Carbon\Carbon::parse($laporan->tgl_lapor)->translatedFormat('d F Y') : '-' }}</p>
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-600 p-3 text-sm text-slate-300">Tidak ada laporan kerusakan aktif.</p>
                @endforelse
            </div>
        </section>

        <section class="pb-6 text-center text-xs text-slate-300">
            <p>Jika menemukan kerusakan, segera laporkan ke unit sarpras.</p>
            @if($waAdmin)
                <a href="https://wa.me/{{ $waAdmin }}" class="mt-3 inline-flex items-center rounded-full bg-emerald-500 px-5 py-2 text-sm font-semibold text-slate-900 transition hover:bg-emerald-400">
                    Hubungi Admin Sarpras
                </a>
            @endif
        </section>
    </main>
</body>
</html>

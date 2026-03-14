<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIM-SARPRAS | Puskesmas Bendan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-welcome-page min-h-screen antialiased">
    <div class="relative min-h-screen overflow-hidden">
        <div class="theme-welcome-canvas absolute inset-0 -z-20"></div>

        <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-5 py-5 lg:px-8">
            <a href="/" class="inline-flex items-center gap-3">
                <img src="{{ asset('images/logo-Puskesmas.png') }}" alt="Logo Puskesmas" class="h-10 w-10 rounded-xl bg-white p-1 shadow-sm">
                <div>
                    <p class="text-[11px] font-medium tracking-[0.08em] text-blue-800">SIMSARPRAS</p>
                    <p class="text-sm font-semibold text-slate-800">Puskesmas Bendan</p>
                </div>
            </a>
            <nav class="flex items-center gap-2 text-sm font-medium">
                @auth
                    <a href="{{ url('/admin') }}" class="rounded-xl bg-blue-800 px-4 py-2 text-white transition hover:bg-blue-900">Panel Admin</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-700 transition hover:border-slate-400">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-white transition hover:bg-slate-800">Register</a>
                    @endif
                @endauth
            </nav>
        </header>

        <main class="mx-auto grid w-full max-w-6xl gap-10 px-5 pb-16 pt-8 lg:grid-cols-[1.18fr_0.82fr] lg:gap-12 lg:px-8 lg:pb-24 lg:pt-14">
            <section>
                <p class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-[11px] font-medium tracking-[0.08em] text-blue-800">
                    Sistem Sarpras Terpadu
                </p>
                <h1 class="mt-5 text-3xl font-semibold leading-tight text-slate-900 sm:text-4xl lg:text-[2.7rem]">
                    Tampilan data lebih rapi, kerja harian lebih mudah.
                </h1>
                <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-600">
                    Kelola inventaris, jadwal pemeliharaan, dan laporan dalam satu alur yang sederhana, jelas, dan siap audit.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ auth()->check() ? url('/admin') : route('login') }}" class="rounded-xl bg-blue-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-900">
                        Buka Dashboard
                    </a>
                    <a href="#fitur" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                        Lihat Ringkasan
                    </a>
                </div>

                <div class="mt-11 grid gap-4 sm:grid-cols-3">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xl font-semibold text-slate-900">24/7</p>
                        <p class="mt-1 text-xs tracking-[0.04em] text-slate-500">Akses Sistem</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xl font-semibold text-slate-900">QR</p>
                        <p class="mt-1 text-xs tracking-[0.04em] text-slate-500">Label Aset</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xl font-semibold text-slate-900">MFK</p>
                        <p class="mt-1 text-xs tracking-[0.04em] text-slate-500">Monitoring Rutin</p>
                    </article>
                </div>
            </section>

            <section id="fitur" class="theme-welcome-panel grid gap-6 p-6 lg:p-8">
                <article>
                    <h2 class="text-sm font-semibold tracking-[0.03em] text-blue-800">Monitoring Utilitas</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Pemantauan listrik, air, dan indikator fasilitas dengan data historis yang konsisten.</p>
                </article>
                <article class="border-t border-slate-200 pt-6">
                    <h2 class="text-sm font-semibold tracking-[0.03em] text-blue-800">Kalibrasi & Maintenance</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Jadwal kalibrasi dan pemeliharaan tersusun rapi agar tidak terlewat.</p>
                </article>
                <article class="border-t border-slate-200 pt-6">
                    <h2 class="text-sm font-semibold tracking-[0.03em] text-blue-800">Laporan Siap Audit</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Laporan berkala dapat dicetak langsung dengan format yang mudah dibaca.</p>
                </article>
            </section>
        </main>

        <footer class="mx-auto flex w-full max-w-6xl flex-col gap-2 border-t border-slate-200 px-5 py-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
            <p>&copy; {{ date('Y') }} SIM-SARPRAS UPT Puskesmas Bendan.</p>
            <p>Kota Pekalongan, Jawa Tengah</p>
        </footer>
    </div>
</body>
</html>


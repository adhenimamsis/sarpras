<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 | Sesi Berakhir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-welcome-page min-h-screen antialiased">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4">
        <div class="theme-welcome-canvas absolute inset-0 -z-20"></div>

        <main class="theme-welcome-panel w-full max-w-lg p-8 text-center">
            <p class="text-sm font-medium tracking-[0.08em] text-blue-800">Error 419</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Sesi login sudah berakhir</h1>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                Halaman kemungkinan terlalu lama terbuka atau token keamanan sudah kadaluarsa.
                Silakan muat ulang halaman login lalu coba lagi.
            </p>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl border border-blue-800 bg-blue-800 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-900">
                    Kembali ke Login
                </a>
                <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:border-slate-400 hover:text-slate-900">
                    Muat Ulang Halaman Sebelumnya
                </a>
            </div>
        </main>
    </div>
</body>
</html>

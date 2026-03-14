<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\Setting::getValue('nama_puskesmas', 'SimSarpras Bendan') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="theme-welcome-page min-h-screen antialiased">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10">
            <div class="theme-welcome-canvas absolute inset-0 -z-20"></div>

            <a href="/" class="mb-6 flex flex-col items-center gap-3 text-center">
                <img src="{{ asset('images/logo-Puskesmas.png') }}" alt="Logo Puskesmas" class="h-14 w-14 rounded-2xl bg-white p-2 shadow-sm">
                <div>
                    <h1 class="text-lg font-semibold tracking-[0.02em] text-slate-800">
                        {{ \App\Models\Setting::getValue('nama_puskesmas', 'Puskesmas Bendan') }}
                    </h1>
                    <p class="text-[11px] font-medium tracking-[0.06em] text-blue-800">Sistem Informasi Sarpras</p>
                </div>
            </a>

            <div class="theme-welcome-panel w-full max-w-md px-7 py-7 sm:px-8 sm:py-8">
                <div class="mb-6 text-center">
                    <h2 class="text-lg font-semibold text-slate-900">Selamat Datang</h2>
                    <p class="text-xs text-slate-500">Masuk untuk melanjutkan pengelolaan data sarpras</p>
                </div>

                <div class="welcome-auth-content">
                    {{ $slot }}
                </div>
            </div>

            <footer class="mt-6 text-center text-[11px] font-medium tracking-[0.04em] text-slate-500">
                &copy; {{ date('Y') }} Sistem Informasi MFK & Sarpras
            </footer>
        </div>
    </body>
</html>


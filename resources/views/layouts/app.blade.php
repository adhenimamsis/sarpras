<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\Setting::getValue('nama_puskesmas', 'SimSarpras Bendan') }}</title>

        <link rel="icon" type="image/svg+xml" href="https://www.svgrepo.com/show/422116/health-hospital-medical.svg">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @filamentStyles
        @filamentScripts
    </head>
    <body class="theme-welcome-page antialiased">
        <div class="relative flex min-h-screen flex-col">
            <div class="theme-welcome-canvas absolute inset-0 -z-20"></div>

            @include('layouts.navigation')

            @isset($header)
                <header class="theme-welcome-soft-panel sticky top-0 z-20 border-b border-slate-200/80">
                    <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between">
                            <div class="text-lg font-semibold tracking-tight text-slate-900">
                                {{ $header }}
                            </div>
                            <div id="header-actions" class="flex items-center gap-2"></div>
                        </div>
                    </div>
                </header>
            @endisset

            <main class="welcome-app-content mx-auto w-full max-w-7xl flex-grow px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>

            <footer class="theme-welcome-soft-panel border-t border-slate-200/80 py-6">
                <div class="mx-auto max-w-7xl px-4">
                    <div class="flex flex-col items-center justify-between gap-2 md:flex-row">
                        <p class="text-[11px] font-medium text-slate-500">
                            &copy; {{ date('Y') }} {{ \App\Models\Setting::getValue('nama_puskesmas', 'UPT Puskesmas Bendan') }}.
                            <span class="hidden sm:inline">SIM-SARPRAS & MFK Digital.</span>
                        </p>
                        <div class="flex items-center gap-4 text-[10px] font-semibold tracking-[0.08em] text-slate-500">
                            <span>Sistem Informasi Sarana Prasarana</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        @livewire('notifications')
    </body>
</html>

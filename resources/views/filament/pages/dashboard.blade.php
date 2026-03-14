<x-filament-panels::page>
    <div class="space-y-7">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-base font-semibold text-slate-900">Ringkasan Operasional</h2>
            <p class="mt-1 text-sm text-slate-600">
                Dashboard menampilkan kondisi aset, utilitas, dan jadwal pemeliharaan yang perlu ditindaklanjuti.
            </p>
        </section>

        <div>
            @livewire(\App\Filament\Widgets\StatsOverview::class)
        </div>

        <div class="grid grid-cols-1 gap-7 lg:grid-cols-3">
            <section class="space-y-4 lg:col-span-2">
                <div class="px-1">
                    <h2 class="text-sm font-semibold text-slate-900">Tren Pemeliharaan & Kerusakan</h2>
                    <p class="text-xs text-slate-500">Pergerakan laporan dan tindakan perawatan.</p>
                </div>
                @livewire(\App\Filament\Widgets\LaporanChart::class)
            </section>

            <section class="space-y-4">
                <div class="px-1">
                    <h2 class="text-sm font-semibold text-slate-900">Status Kepatuhan MFK</h2>
                    <p class="text-xs text-slate-500">Ringkasan status kepatuhan fasilitas.</p>
                </div>
                @if(class_exists(\App\Filament\Widgets\MfkStatusOverview::class))
                    @livewire(\App\Filament\Widgets\MfkStatusOverview::class)
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center">
                        <x-filament::icon icon="heroicon-o-squares-plus" class="mx-auto mb-2 h-7 w-7 text-slate-400"/>
                        <p class="text-xs text-slate-500">Widget MfkStatusOverview belum tersedia.</p>
                    </div>
                @endif
            </section>
        </div>

        <div class="grid grid-cols-1 gap-7 xl:grid-cols-2">
            <section class="space-y-4">
                <div class="px-1">
                    <h2 class="text-sm font-semibold text-slate-900">Atensi APAR & Kebakaran</h2>
                    <p class="text-xs text-slate-500">Daftar item yang perlu tindak lanjut segera.</p>
                </div>
                @livewire(\App\Filament\Widgets\AparIssueTable::class)
            </section>

            <section class="space-y-4">
                <div class="px-1">
                    <h2 class="text-sm font-semibold text-slate-900">Jadwal Servis Aset (H-30)</h2>
                    <p class="text-xs text-slate-500">Aset yang mendekati jadwal pemeliharaan.</p>
                </div>
                @if(class_exists(\App\Filament\Widgets\MfkMaintenanceTable::class))
                    @livewire(\App\Filament\Widgets\MfkMaintenanceTable::class)
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-6">
                        <p class="text-center text-xs italic text-slate-500">Widget MfkMaintenanceTable belum tersedia.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @can('reports.view.sensitive')
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-base font-semibold text-slate-900">Laporan KIB & Inventaris</h3>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">
                Rekapitulasi aset berdasarkan kategori KIB.
            </p>

            <div class="mt-4 space-y-2">
                <x-filament::button wire:click="cetakKib('B')" icon="heroicon-m-printer" color="gray" size="sm" outlined>
                    Cetak KIB B (Peralatan)
                </x-filament::button>
                <x-filament::button wire:click="cetakKib('C')" icon="heroicon-m-printer" color="gray" size="sm" outlined>
                    Cetak KIB C (Gedung)
                </x-filament::button>
            </div>
        </section>
        @endcan

        @can('reports.view.sensitive')
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-base font-semibold text-slate-900">Laporan MFK & Utilitas</h3>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">
                Ringkasan pemeliharaan fasilitas, listrik, air, dan status APAR.
            </p>

            <div class="mt-4">
                <x-filament::button wire:click="cetakMfk" icon="heroicon-m-document-chart-bar" color="warning" class="w-full">
                    Cetak Rekap Bulanan MFK
                </x-filament::button>
            </div>
        </section>
        @endcan

        @can('reports.view.sensitive')
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-base font-semibold text-slate-900">Legalitas & Kalibrasi</h3>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">
                Daftar jadwal kalibrasi alat dan dokumen legalitas sarpras.
            </p>

            <div class="mt-4 space-y-2">
                <x-filament::button wire:click="cetakKalibrasi" icon="heroicon-m-academic-cap" color="success" class="w-full">
                    Jadwal Kalibrasi Alkes
                </x-filament::button>
                <x-filament::button wire:click="cetakLegalitas" icon="heroicon-m-scale" color="gray" size="sm" outlined>
                    Daftar Sertifikat & IMB
                </x-filament::button>
            </div>
        </section>
        @endcan
    </div>

    @can('reports.view.sensitive')
    <section class="mt-7 rounded-2xl border border-slate-200 bg-white p-7">
        <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Laporan Bulanan Terpadu</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Satu dokumen PDF untuk rangkuman inventaris, MFK, kerusakan, dan utilitas.
                </p>
            </div>
            <div class="shrink-0">
                <x-filament::button wire:click="cetakLaporanTerpadu" icon="heroicon-m-printer">
                    Download Laporan Terpadu (PDF)
                </x-filament::button>
            </div>
        </div>
    </section>
    @else
    <section class="mt-7 rounded-2xl border border-amber-200 bg-amber-50 p-6">
        <h3 class="text-sm font-semibold text-amber-900">Akses Laporan Sensitif Dibatasi</h3>
        <p class="mt-1 text-xs leading-relaxed text-amber-800">
            Role Anda hanya bisa mengakses laporan operasional. Tombol laporan sensitif disembunyikan untuk mencegah error 403.
        </p>
    </section>
    @endcan

    <div class="mt-3 text-center">
        <p class="text-[11px] text-slate-500">
            Sistem Informasi Sarpras Puskesmas Bendan - Data diperbarui secara real-time berdasarkan input harian.
        </p>
    </div>
</x-filament-panels::page>

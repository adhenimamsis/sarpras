<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-7 md:grid-cols-4">
        <div class="space-y-4 md:col-span-1">
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-3 border-b border-slate-200 pb-2 text-sm font-semibold text-slate-700">Pilih Ruangan</h3>

                <select
                    wire:model.live="selectedRuanganId"
                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($allRuangan as $r)
                        <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                    @endforeach
                </select>

                @if($selectedRuanganId)
                    @php $selected = $allRuangan->find($selectedRuanganId); @endphp
                    <div class="mt-2 rounded-lg border border-blue-200 bg-blue-50 p-2">
                        <p class="text-center text-[11px] font-medium text-blue-800">
                            Siap update lokasi: {{ $selected?->nama_ruangan }}
                        </p>
                    </div>
                @endif

                <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] leading-relaxed text-slate-600">
                        <strong>Cara update lokasi:</strong><br>
                        Pilih ruangan di atas, lalu klik denah untuk menaruh titik lokasi.
                    </p>
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-3 border-b border-slate-200 pb-2 text-sm font-semibold text-slate-700">Legenda Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center text-xs text-slate-600">
                        <span class="mr-2 h-3 w-3 rounded-full bg-green-500"></span>
                        Semua aset baik
                    </div>
                    <div class="flex items-center text-xs text-slate-600">
                        <span class="mr-2 h-3 w-3 rounded-full bg-yellow-500"></span>
                        Ada rusak ringan
                    </div>
                    <div class="flex items-center text-xs font-semibold text-slate-700">
                        <span class="mr-2 h-3 w-3 rounded-full bg-red-600"></span>
                        Ada rusak berat
                    </div>
                </div>

                <hr class="my-4 border-slate-200">

                <h3 class="mb-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Daftar Terpetakan</h3>
                <div class="custom-scrollbar max-h-60 space-y-1 overflow-y-auto pr-2">
                    @foreach($allRuangan->whereNotNull('koordinat_x') as $r)
                        @php
                            $mapColor = [
                                'red' => 'bg-red-600', 'danger' => 'bg-red-600',
                                'yellow' => 'bg-yellow-500', 'warning' => 'bg-yellow-500',
                                'green' => 'bg-green-500', 'success' => 'bg-green-500',
                            ][$r->status_warna] ?? 'bg-gray-500';
                        @endphp
                        <button
                            wire:click="openDetail({{ $r->id }})"
                            class="group flex w-full items-center justify-between rounded-lg p-2 text-left text-[10px] transition hover:bg-slate-50">
                            <div class="flex items-center">
                                <span class="mr-2 h-2 w-2 rounded-full {{ $mapColor }}"></span>
                                <span class="font-medium text-slate-700 group-hover:text-slate-900">{{ $r->nama_ruangan }}</span>
                            </div>
                            <x-filament::icon icon="heroicon-m-chevron-right" class="h-3 w-3 text-slate-300 group-hover:text-slate-500"/>
                        </button>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="md:col-span-3">
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Peta Interaktif Puskesmas Bendan</h2>
                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] font-medium text-slate-600">Live Status</span>
                </div>

                <div class="group relative inline-block cursor-crosshair overflow-hidden rounded-xl border border-slate-200 bg-slate-50 shadow-sm">
                    <img
                        id="petaPuskesmas"
                        src="{{ asset('images/denah-Puskesmas.png') }}"
                        alt="Denah Puskesmas"
                        class="block h-auto w-full"
                        onclick="updateCoordinate(event)">

                    @foreach($allRuangan as $item)
                        @if($item->koordinat_x && $item->koordinat_y)
                            @php
                                $warnaMapping = [
                                    'red' => 'bg-red-600', 'danger' => 'bg-red-600',
                                    'yellow' => 'bg-yellow-500', 'warning' => 'bg-yellow-500',
                                    'green' => 'bg-green-500', 'success' => 'bg-green-500',
                                ];
                                $warnaClass = $warnaMapping[$item->status_warna] ?? 'bg-gray-500';
                            @endphp

                            <div
                                class="group/pin absolute cursor-pointer transition-all hover:z-50"
                                wire:click="openDetail({{ $item->id }})"
                                style="top: {{ $item->koordinat_y }}%; left: {{ $item->koordinat_x }}%; transform: translate(-50%, -50%);">
                                <div class="relative">
                                    <div class="h-5 w-5 {{ $warnaClass }} rounded-full border-2 border-white shadow-sm transition-transform hover:scale-125 active:scale-95"></div>
                                    <div class="pointer-events-none absolute bottom-7 left-1/2 z-50 -translate-x-1/2 whitespace-nowrap rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] text-slate-700 opacity-0 shadow-md transition-all group-hover/pin:opacity-100">
                                        <p class="font-bold">{{ $item->nama_ruangan }}</p>
                                        <p class="text-[8px] italic text-slate-500">{{ $item->assets_count }} Aset Terdata</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <x-filament::modal id="detail-ruangan-modal" width="4xl">
        @if($detailRuangan)
            <x-slot name="header">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ in_array($detailRuangan->status_warna, ['red', 'danger']) ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-800' }}">
                        <x-filament::icon icon="heroicon-o-home-modern" class="h-7 w-7"/>
                    </div>
                    <div>
                        <h2 class="text-2xl font-semibold leading-tight text-slate-900">
                            {{ $detailRuangan->nama_ruangan }}
                        </h2>
                        <p class="font-mono text-xs uppercase tracking-[0.14em] text-slate-500">{{ $detailRuangan->kode_ruangan }}</p>
                    </div>
                </div>
            </x-slot>

            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Level Lantai</span>
                        <p class="text-sm font-semibold text-slate-900">{{ $detailRuangan->lantai ?? 'Dasar' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Inventory</span>
                        <p class="text-sm font-semibold text-slate-900">{{ $detailRuangan->assets->count() }} Item</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Status MFK</span>
                        <div class="flex items-center gap-2">
                            @php
                                $isAlert = in_array($detailRuangan->status_warna, ['red', 'danger']);
                            @endphp
                            <span class="h-2 w-2 rounded-full {{ $isAlert ? 'bg-red-600' : 'bg-green-600' }}"></span>
                            <p class="text-sm font-semibold {{ $isAlert ? 'text-red-600' : 'text-green-600' }}">
                                {{ $isAlert ? 'Ada Kerusakan' : 'Fasilitas Baik' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h4 class="mb-4 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-700">
                        <x-filament::icon icon="heroicon-m-document-check" class="h-4 w-4"/>
                        Informasi Ruang & Legalitas
                    </h4>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-5 md:grid-cols-3">
                        <div>
                            <p class="mb-1 text-[10px] font-semibold uppercase text-slate-500">Luas Ruangan</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $detailRuangan->luas_ruangan ?? '-' }} m2</p>
                        </div>
                        <div>
                            <p class="mb-1 text-[10px] font-semibold uppercase text-slate-500">Kapasitas</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $detailRuangan->kapasitas ?? '-' }} Orang</p>
                        </div>
                        <div>
                            <p class="mb-1 text-[10px] font-semibold uppercase text-slate-500">Tahun Renovasi</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $detailRuangan->tahun_renovasi ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-slate-200 bg-slate-50 text-slate-600">
                            <tr>
                                <th class="p-4 text-[10px] font-semibold uppercase tracking-[0.12em]">Nama Perangkat / Alkes</th>
                                <th class="p-4 text-center text-[10px] font-semibold uppercase tracking-[0.12em]">Kondisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($detailRuangan->assets as $asset)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="p-4 font-medium text-slate-700">{{ $asset->nama_alat }}</td>
                                    <td class="p-4 text-center">
                                        @php
                                            $condStyle = match($asset->kondisi) {
                                                'Rusak Berat' => 'bg-red-50 text-red-700 border-red-200',
                                                'Rusak Ringan' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                default => 'bg-green-50 text-green-700 border-green-200',
                                            };
                                        @endphp
                                        <span class="rounded-full border px-3 py-1 text-[10px] font-bold {{ $condStyle }}">
                                            {{ strtoupper($asset->kondisi) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="p-8 text-center italic text-slate-400">Belum ada inventaris tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex w-full flex-col justify-between gap-3 sm:flex-row">
                    <x-filament::button color="gray" x-on:click="close" outlined icon="heroicon-m-x-mark">Tutup</x-filament::button>
                    <div class="flex gap-2">
                        <x-filament::button wire:click="cetakLaporan({{ $detailRuangan->id }})" color="info" icon="heroicon-m-printer">Cetak Inventaris</x-filament::button>
                        <x-filament::button wire:click="$set('selectedRuanganId', {{ $detailRuangan->id }})" x-on:click="close" color="primary" icon="heroicon-m-map-pin">Reposisi Pin</x-filament::button>
                    </div>
                </div>
            </x-slot>
        @endif
    </x-filament::modal>

    <script>
        function updateCoordinate(event) {
            const selectedId = @this.get('selectedRuanganId');
            if (!selectedId) {
                new FilamentNotification().title('Oops!').body('Pilih ruangan dulu di kiri.').danger().send();
                return;
            }
            const img = document.getElementById('petaPuskesmas');
            const rect = img.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width) * 100;
            const y = ((event.clientY - rect.top) / rect.height) * 100;
            @this.setLocation(x.toFixed(2), y.toFixed(2));
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    </style>
</x-filament-panels::page>


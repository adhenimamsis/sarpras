<x-filament-panels::page>
    <div class="space-y-7">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-slate-900">Konfigurasi Sistem & Identitas</h2>
            <p class="mt-1 text-sm text-slate-600">
                Pengaturan ini digunakan untuk kop surat, judul dashboard, dan identitas resmi Puskesmas.
            </p>
        </section>

        <form wire:submit="save" class="space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white">
                <div class="p-6">
                    {{ $this->form }}
                </div>

                <div class="flex flex-col items-start justify-between gap-4 rounded-b-2xl border-t border-slate-200 bg-slate-50 p-4 md:flex-row md:items-center">
                    <div class="flex items-center gap-3">
                        <x-filament::button
                            type="submit"
                            icon="heroicon-m-cloud-arrow-up"
                            wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </x-filament::button>

                        <div wire:loading wire:target="save" class="flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                            <x-filament::loading-indicator class="h-4 w-4 text-amber-600" />
                            <span class="text-xs text-amber-700">Memperbarui cache sistem</span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500">
                        Perubahan akan langsung digunakan pada seluruh modul laporan.
                    </p>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <h4 class="text-sm font-semibold text-slate-900">Tips Pengisian</h4>
                <p class="mt-1 text-xs leading-relaxed text-slate-600">
                    Pastikan alamat dan nomor telepon puskesmas diisi lengkap agar otomatis muncul pada dokumen laporan.
                </p>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <h4 class="text-sm font-semibold text-slate-900">Catatan Sistem</h4>
                <p class="mt-1 text-xs leading-relaxed text-slate-600">
                    Setelah disimpan, cache aplikasi disegarkan agar data terbaru langsung aktif.
                </p>
            </section>
        </div>
    </div>
</x-filament-panels::page>

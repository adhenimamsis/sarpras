<?php

namespace App\Filament\Pages;

use App\Models\Ruangan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DenahSarpras extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Denah Sarpras';

    protected static ?string $title = 'Visualisasi Lokasi Aset';

    protected static ?string $navigationGroup = 'Sarana & Prasarana';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.denah-sarpras';

    // State untuk filtering dan detail
    public $selectedRuanganId;

    public $activeFloor = 'Lantai 1';

    public $detailRuangan;

    /**
     * Membuka Modal Detail Ruangan (Eager Load Assets & Count).
     */
    public function openDetail($id)
    {
        $this->detailRuangan = Ruangan::withCount('assets')
            ->with(['assets' => function ($query) {
                $query->orderBy('nama_alat', 'asc');
            }])->find($id);

        if (! $this->detailRuangan) {
            Notification::make()->title('Ruangan tidak ditemukan')->danger()->send();

            return;
        }

        $this->dispatch('open-modal', id: 'detail-ruangan-modal');
    }

    /**
     * Ganti Tampilan Lantai (Tab Denah).
     */
    public function setFloor($floor)
    {
        $this->activeFloor = $floor;
    }

    /**
     * Fungsi Pinpoint: Menyimpan koordinat klik pada gambar denah.
     */
    public function setLocation($x, $y)
    {
        if (! $this->selectedRuanganId) {
            Notification::make()
                ->title('Gagal!')
                ->body('Pilih ruangan terlebih dahulu di sidebar kiri sebelum menandai lokasi.')
                ->warning()
                ->send();

            return;
        }

        $ruangan = Ruangan::find($this->selectedRuanganId);

        if ($ruangan) {
            $ruangan->update([
                'koordinat_x' => $x,
                'koordinat_y' => $y,
                'lantai' => $this->activeFloor,
            ]);

            Notification::make()
                ->title('Titik Lokasi Disimpan')
                ->body("Ruang {$ruangan->nama_ruangan} sekarang berada di {$this->activeFloor} (X: $x, Y: $y).")
                ->success()
                ->send();
        }
    }

    /**
     * Mengarahkan ke Route Cetak Daftar Aset per Ruangan.
     */
    public function cetakLaporan($id = null)
    {
        $targetId = $id ?? $this->selectedRuanganId;

        if (! $targetId) {
            Notification::make()->title('Pilih Ruangan!')->danger()->send();

            return;
        }

        return redirect()->route('cetak.ruangan', ['id' => $targetId]);
    }

    /**
     * Alias untuk cetakLaporan agar tombol di Blade tidak error (wire:click="cetakMfk").
     */
    public function cetakMfk($id = null)
    {
        return $this->cetakLaporan($id);
    }

    /**
     * LOGIC WARNA & DATA (Anti-Error assets_count).
     */
    protected function getViewData(): array
    {
        // 1. Ambil data ruangan dengan hitungan aset untuk PIN di denah
        $ruangans = Ruangan::withCount('assets')
            ->with(['assets' => function ($q) {
                $q->select('id', 'ruangan_id', 'kondisi', 'tgl_kalibrasi_selanjutnya', 'status_kalibrasi');
            }])
            ->get()
            ->map(function ($ruangan) {
                // Logic Warna Otomatis berdasarkan kondisi aset di dalamnya
                $hasRusakBerat = $ruangan->assets->where('kondisi', 'Rusak Berat')->count() > 0;
                $hasRusakRingan = $ruangan->assets->where('kondisi', 'Rusak Ringan')->count() > 0;

                // Cek jadwal MFK (Kalibrasi) yang sudah lewat
                $hasLateCalibration = $ruangan->assets->filter(function ($asset) {
                    return $asset->status_kalibrasi
                           && $asset->tgl_kalibrasi_selanjutnya
                           && $asset->tgl_kalibrasi_selanjutnya < now();
                })->count() > 0;

                // Prioritas Warna: Merah (Kritis) > Kuning (Peringatan) > Hijau (Aman)
                if ($hasRusakBerat) {
                    $ruangan->status_warna = 'red';
                } elseif ($hasRusakRingan || $hasLateCalibration) {
                    $ruangan->status_warna = 'yellow';
                } else {
                    $ruangan->status_warna = 'green';
                }

                return $ruangan;
            });

        return [
            'ruanganLantai1' => $ruangans->where('lantai', 'Lantai 1')->values(),
            'ruanganLantai2' => $ruangans->where('lantai', 'Lantai 2')->values(),
            // PENTING: withCount('assets') agar Sidebar tidak error [assets_count]
            'allRuangan' => Ruangan::withCount('assets')->orderBy('nama_ruangan')->get(),
        ];
    }
}

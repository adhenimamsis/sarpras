<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AsetTahunanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return Asset::with('ruangan')
            ->where('tahun_perolehan', $this->tahun)
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Alat/Aset',
            'Kode ASPAK',
            'Merk/Tipe',
            'No Seri',
            'Ruangan',
            'Tahun Perolehan',
            'Kondisi',
            'Status Kalibrasi',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->id,
            $asset->nama_alat,
            $asset->kode_aspak,
            $asset->merk.' / '.$asset->tipe,
            $asset->no_seri,
            $asset->ruangan?->nama_ruangan,
            $asset->tahun_perolehan,
            $asset->kondisi,
            $asset->status_kalibrasi ? 'Sudah Kalibrasi' : 'Belum/Tidak Perlu',
        ];
    }
}

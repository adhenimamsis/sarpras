<?php

namespace App\Imports;

use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RuanganImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        /**
         * NORMALISASI DATA
         * Memastikan nama ruangan rapi (UPPERCASE) dan menghapus spasi berlebih
         * agar pencarian asset per ruangan nantinya tidak error karena typo spasi.
         */
        $namaRuangan = strtoupper(trim($row['nama_ruangan']));
        $kodeRuangan = trim($row['kode_ruangan']);

        /**
         * MAPPING LANTAI
         * Mengambil angka saja dari input (misal "Lantai 2" jadi "2").
         */
        $lantaiRaw = $row['posisi_lantai'] ?? $row['lantai'] ?? '1';
        $lantaiClean = filter_var($lantaiRaw, FILTER_SANITIZE_NUMBER_INT);

        /*
         * Menggunakan updateOrCreate agar data bersifat idempotent.
         * Jika kode_ruangan sudah ada, sistem hanya memperbarui info terbaru.
         */
        return Ruangan::updateOrCreate(
            ['kode_ruangan' => $kodeRuangan],
            [
                'nama_ruangan' => $namaRuangan,
                'lantai' => $lantaiClean ?: 1, // Default ke lantai 1 jika kosong
                'keterangan' => $row['keterangan_tambahan'] ?? $row['keterangan'] ?? '-',
            ]
        );
    }

    /**
     * VALIDASI DATA IMPORT.
     */
    public function rules(): array
    {
        return [
            'nama_ruangan' => 'required|string|max:255',
            'kode_ruangan' => 'required|string',
            '*.nama_ruangan' => 'required', // Memastikan tiap baris ada namanya
        ];
    }

    /**
     * Custom Heading Mapping
     * Membantu sistem mengenali heading meskipun ada sedikit perbedaan spasi/huruf.
     */
    public function prepareForValidation($data, $index)
    {
        // Trim semua key header untuk menghindari error "Undefined index" karena spasi di Excel
        return array_combine(
            array_map(fn ($key) => strtolower(str_replace(' ', '_', trim($key))), array_keys($data)),
            array_values($data)
        );
    }
}

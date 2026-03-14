<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Exports\AsetTahunanExport;
use App\Filament\Resources\AssetResource;
use App\Models\Asset;
use App\Models\Ruangan;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. MANAJEMEN DATA (IMPORT & TEMPLATE)
            Actions\ActionGroup::make([
                // Download Template CSV
                Actions\Action::make('downloadTemplate')
                    ->label('Download Template CSV')
                    ->color('gray')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        $header = [
                            'Kategori KIB (KIB_A/KIB_B/KIB_C/KIB_D/KIB_E)',
                            'Nama Barang',
                            'Kode Barang',
                            'No Register',
                            'Merk/Tipe',
                            'No Seri/No Sertifikat',
                            'Nama Ruangan',
                            'Tahun Perolehan',
                            'Harga Perolehan',
                            'Kondisi (Baik/Rusak Ringan/Rusak Berat)',
                            'Alamat/Lokasi',
                            'Luas (m2)',
                            'Asal Usul',
                        ];

                        $callback = function () use ($header) {
                            $file = fopen('php://output', 'w');
                            fputcsv($file, $header);
                            // Contoh data dummy yang valid
                            fputcsv($file, ['KIB_B', 'USG 4 DIMENSI', '02.10.01.02.001', '0001', 'GE HEALTHCARE', 'SN123456', 'Poli KIA', '2024', '450000000', 'Baik', 'Puskesmas Bendan', '0', 'APBD']);
                            fclose($file);
                        };

                        return response()->streamDownload($callback, 'template_kib_puskesmas_v3.csv');
                    }),

                // Import Data Terintegrasi
                Actions\Action::make('importExcel')
                    ->label('Import Data KIB')
                    ->color('success')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->form([
                        FileUpload::make('attachment')
                            ->label('Pilih File Excel/CSV')
                            ->disk('public')
                            ->directory('temp-imports')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'text/csv',
                            ])
                            ->maxSize(5120)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $attachmentPath = (string) ($data['attachment'] ?? '');
                        if ($attachmentPath === '' || ! Storage::disk('public')->exists($attachmentPath)) {
                            Notification::make()
                                ->title('Gagal Import')
                                ->body('File import tidak ditemukan. Silakan unggah ulang file.')
                                ->danger()
                                ->persistent()
                                ->send();

                            return;
                        }

                        $filePath = Storage::disk('public')->path($attachmentPath);

                        try {
                            $rows = Excel::toArray([], $filePath)[0];
                            $successCount = 0;

                            foreach (array_slice($rows, 1) as $row) {
                                // Lewati jika nama barang kosong
                                if (empty($row[1])) {
                                    continue;
                                }

                                // 1. Logika Pencarian Ruangan (Smart Search)
                                $namaRuangan = trim($row[6]);
                                $ruanganId = null;
                                if (! empty($namaRuangan)) {
                                    $ruangan = Ruangan::where('nama_ruangan', 'like', "%{$namaRuangan}%")->first();
                                    $ruanganId = $ruangan?->id;
                                }

                                // 2. Sanitasi Harga (Hapus Rp, Titik, Spasi)
                                $hargaRaw = $row[8];
                                $hargaBersih = preg_replace('/[^0-9]/', '', $hargaRaw);

                                // 3. Mapping Kondisi sesuai Enum Database
                                $kondisiImport = strtoupper(trim($row[9]));
                                $kondisiFinal = match ($kondisiImport) {
                                    'B', 'BAIK' => 'Baik',
                                    'KB', 'KURANG BAIK', 'RUSAK RINGAN' => 'Rusak Ringan',
                                    'RB', 'RUSAK BERAT' => 'Rusak Berat',
                                    default => 'Baik',
                                };

                                // 4. Eksekusi Create Data
                                Asset::create([
                                    'kategori_kib' => $row[0] ?? 'KIB_B',
                                    'nama_alat' => strtoupper(trim($row[1])),
                                    'kode_aspak' => $row[2],
                                    'no_register' => $row[3],
                                    'merk' => strtoupper($row[4]),
                                    'no_sertifikat' => $row[5],
                                    'ruangan_id' => $ruanganId,
                                    'tahun_perolehan' => $row[7],
                                    'harga_perolehan' => $hargaBersih ?: 0,
                                    'kondisi' => $kondisiFinal,
                                    'alamat' => strtoupper($row[10]),
                                    'luas_meter' => $row[11] ?? 0,
                                    'asal_usul' => $row[12] ?? 'APBD',
                                ]);
                                $successCount++;
                            }

                            Notification::make()
                                ->title('Import Berhasil')
                                ->body("$successCount data aset berhasil masuk ke sistem.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Log::error('Gagal import data aset.', [
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Gagal Import')
                                ->body('Terjadi kesalahan saat membaca file. Pastikan format template sesuai.')
                                ->danger()
                                ->persistent()
                                ->send();
                        } finally {
                            Storage::disk('public')->delete($attachmentPath);
                        }
                    }),
            ])->label('Kelola Data')->icon('heroicon-m-server-stack')->color('gray'),

            // 2. EXPORT & REPORTING
            Actions\ActionGroup::make([
                ExportAction::make()
                    ->label('Export Excel (Sesuai Filter)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info'),

                Actions\Action::make('exportTahunan')
                    ->label('Cetak Rekap Tahunan')
                    ->icon('heroicon-o-calendar-days')
                    ->color('warning')
                    ->form([
                        Select::make('tahun')
                            ->options(array_combine(range(date('Y'), 2010), range(date('Y'), 2010)))
                            ->default(date('Y'))
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        return Excel::download(
                            new AsetTahunanExport($data['tahun']),
                            "rekap-kib-puskesmas-{$data['tahun']}.xlsx"
                        );
                    }),
            ])->label('Laporan')->icon('heroicon-m-document-arrow-up')->color('info'),

            // 3. TOMBOL TAMBAH
            Actions\CreateAction::make()
                ->label('Tambah Aset')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\AssetStatsOverview::class,
        ];
    }
}

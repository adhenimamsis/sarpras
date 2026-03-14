<?php

namespace App\Filament\Resources\RuanganResource\Pages;

use App\Filament\Resources\RuanganResource;
use App\Imports\RuanganImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListRuangans extends ListRecords
{
    protected static string $resource = RuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Tombol Download Template (Sudah diupdate dengan field Legalitas)
            Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function (): StreamedResponse {
                    // Header disesuaikan dengan field terbaru di database dan model
                    $headers = [
                        'nama_ruangan',
                        'kode_ruangan',
                        'gedung',
                        'lantai',
                        'status_tanah',
                        'no_sertifikat',
                        'asal_usul',
                        'penggunaan',
                        'alamat_lokasi',
                        'keterangan',
                    ];

                    return response()->streamDownload(function () use ($headers) {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, $headers);

                        // Contoh data agar user tidak bingung formatnya
                        fputcsv($handle, [
                            'Poli Umum',
                            'RU-001',
                            'Gedung Utama',
                            'Lantai 1',
                            'Hak Pakai',
                            '12.03.01.01.1.00001',
                            'APBD 2023',
                            'Pelayanan',
                            'Jl. Merdeka No. 10',
                            'Dekat pintu masuk utama',
                        ]);

                        fclose($handle);
                    }, 'template_ruangan_puskesmas.csv');
                }),

            // 2. Tombol Import dari Excel
            Action::make('importExcel')
                ->label('Import Excel/CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file_excel')
                        ->label('Pilih File')
                        ->disk('public')
                        ->directory('imports')
                        ->required()
                        ->maxSize(5120)
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ]),
                ])
                ->action(function (array $data) {
                    $filePath = (string) ($data['file_excel'] ?? '');
                    if ($filePath === '' || ! Storage::disk('public')->exists($filePath)) {
                        Notification::make()
                            ->title('Gagal Import Data')
                            ->body('File import tidak ditemukan. Silakan unggah ulang file.')
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    try {
                        Excel::import(new RuanganImport, Storage::disk('public')->path($filePath));

                        Notification::make()
                            ->title('Data Ruangan Berhasil Diimport!')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Log::error('Gagal import data ruangan.', [
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Gagal Import Data')
                            ->body('Periksa kembali format file Anda sesuai template.')
                            ->danger()
                            ->persistent() // Agar notifikasi tidak hilang sampai dibaca
                            ->send();
                    } finally {
                        Storage::disk('public')->delete($filePath);
                    }
                }),

            Actions\CreateAction::make()
                ->label('Tambah Ruangan Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
}

<?php

namespace App\Filament\Resources\KalibrasiResource\Pages;

use App\Filament\Resources\KalibrasiResource;
use App\Models\Asset;
use Filament\Resources\Pages\CreateRecord;

class CreateKalibrasi extends CreateRecord
{
    protected static string $resource = KalibrasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Hebatnya Filament: Setelah simpan kalibrasi, kita update master data asetnya
    protected function afterCreate(): void
    {
        $data = $this->record;

        $asset = Asset::find($data->asset_id);
        if ($asset) {
            $asset->update([
                'tgl_kalibrasi_terakhir' => $data->tgl_kalibrasi,
                'tgl_kalibrasi_selanjutnya' => $data->tgl_kadaluarsa,
                'status_kalibrasi' => ($data->hasil === 'Layak') ? true : false,
            ]);
        }
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data Kalibrasi berhasil disimpan dan Master Aset telah diperbarui!';
    }
}

<?php

namespace App\Filament\Resources\KalibrasiResource\Pages;

use App\Filament\Resources\KalibrasiResource;
use App\Models\Asset;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKalibrasi extends EditRecord
{
    protected static string $resource = KalibrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Jika data kalibrasi diedit, master aset juga harus ikut sinkron
    protected function afterSave(): void
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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Perubahan data kalibrasi berhasil disinkronkan ke Master Aset!';
    }
}

<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Resources\Pages\Page;

class EditActivityLog extends Page
{
    protected static string $resource = ActivityLogResource::class;

    public function mount(): void
    {
        // Proteksi: Jika ada yang coba tembak URL /edit, langsung blokir
        abort(403, 'Jejak audit tidak boleh diubah, Bos!');
    }
}

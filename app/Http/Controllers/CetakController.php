<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Ruangan;
use Illuminate\View\View;

class CetakController extends Controller
{
    public function ruangan(int $id): View
    {
        $ruangan = Ruangan::with('assets')->findOrFail($id);

        return view('cetak.ruangan', compact('ruangan'));
    }

    public function assetLabel(Asset $record): View
    {
        $record->loadMissing('ruangan');

        return view('cetak.label-asset', ['asset' => $record]);
    }

    public function assetBulk(string $ids): View
    {
        $assetIds = collect(explode(',', $ids))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        abort_if($assetIds->isEmpty(), 404);

        $assets = Asset::with('ruangan')
            ->whereIn('id', $assetIds)
            ->get();

        abort_if($assets->isEmpty(), 404);

        return view('cetak.label-bulk', compact('assets'));
    }
}

<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;

class AssetObserver
{
    /**
     * Logika saat aset baru ditambahkan.
     */
    public function created(Asset $asset): void
    {
        ActivityLog::create([
            'user_id' => Auth::id() ?? 1, // Default ke admin jika via seeder
            'asset_name' => $asset->nama_alat,
            'action' => 'TAMBAH ASET',
            'changes' => ['data' => $asset->only(['no_register', 'kondisi', 'ruangan_id'])],
        ]);
    }

    /**
     * Logika saat data aset diubah (CCTV Inti).
     */
    public function updated(Asset $asset): void
    {
        // Hanya catat jika ada perubahan pada kolom penting
        $changes = array_intersect_key(
            $asset->getChanges(),
            array_flip(['kondisi', 'ruangan_id', 'harga_perolehan', 'is_active'])
        );

        if (! empty($changes)) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'asset_name' => $asset->nama_alat,
                'action' => 'PERUBAHAN DATA',
                'changes' => [
                    'sebelum' => array_intersect_key($asset->getOriginal(), $changes),
                    'sesudah' => $changes,
                ],
            ]);
        }
    }

    /**
     * Logika saat aset dihapus.
     */
    public function deleted(Asset $asset): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'asset_name' => $asset->nama_alat,
            'action' => 'HAPUS ASET',
            'changes' => ['last_data' => $asset->only(['no_register', 'ruangan_id'])],
        ]);
    }
}

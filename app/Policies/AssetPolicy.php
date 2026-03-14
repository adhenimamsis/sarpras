<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    /**
     * Siapa saja yang bisa melihat daftar aset?
     * Semua user yang aktif bisa melihat.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Siapa yang bisa melihat detail aset tertentu?
     */
    public function view(User $user, Asset $asset): bool
    {
        return $user->is_active;
    }

    /**
     * Siapa yang boleh menambah aset baru?
     * Hanya Admin dan Staff Sarpras.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    /**
     * Siapa yang boleh mengedit data aset?
     * Admin, Staff, dan Teknisi (untuk update status maintenance).
     */
    public function update(User $user, Asset $asset): bool
    {
        return in_array($user->role, ['admin', 'staff', 'teknisi']);
    }

    /**
     * Siapa yang boleh MENGHAPUS aset?
     * SANGAT KETAT: Hanya Admin yang boleh hapus aset negara.
     */
    public function delete(User $user, Asset $asset): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Aturan untuk hapus massal (Bulk Delete) di Filament.
     */
    public function deleteAny(User $user): bool
    {
        return $user->role === 'admin';
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan form profil user.
     * Dioptimalkan untuk menampilkan data user yang sedang login di SimSarpras.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui informasi profil.
     * Melakukan validasi otomatis via ProfileUpdateRequest.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Mengisi data yang sudah divalidasi
        $user->fill($request->validated());

        // Jika email berubah, reset status verifikasi (Standar Keamanan)
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun user (Gunakan dengan hati-hati).
     * Membutuhkan konfirmasi password untuk keamanan data Sarpras.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Logout user sebelum penghapusan permanen
        Auth::logout();

        // Hapus user dari database
        $user->delete();

        // Invalidate session agar tidak bisa diakses kembali
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'Akun berhasil dihapus dari sistem.');
    }
}

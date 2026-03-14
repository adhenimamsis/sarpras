<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * Atribut yang dapat diisi (Mass Assignable).
     * Diselaraskan dengan migration terbaru (NIP & Phone).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'phone_number',
        'role',         // admin, staff, teknisi, kapus
        'is_active',    // Status aktif akun
        'avatar_url',   // Foto profil user
        'last_login_at',
    ];

    /**
     * Atribut yang disembunyikan dari serialisasi JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut untuk tipe data yang tepat.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * FILAMENT: ACCESS CONTROL
     * Verifikasi hak akses dan status keaktifan user.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // User hanya bisa masuk jika perannya diizinkan DAN akunnya berstatus aktif
        return in_array($this->role, ['admin', 'staff', 'teknisi', 'kapus'])
               && $this->is_active === true;
    }

    /**
     * FILAMENT: DISPLAY NAME
     * Menampilkan nama dan role di pojok kanan atas dashboard.
     */
    public function getFilamentName(): string
    {
        $name = $this->attributes['name'] ?? $this->name ?? 'USER';
        $role = $this->attributes['role'] ?? $this->role ?? 'staff';

        return "{$name} [".strtoupper($role).']';
    }

    /**
     * FILAMENT: AVATAR
     * Mengambil URL foto profil dari disk public.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        $avatar = $this->attributes['avatar_url'] ?? null;

        return is_string($avatar) && $avatar !== '' ? Storage::url($avatar) : null;
    }

    /**
     * ROLE HELPERS
     * Digunakan untuk Gate, Policy, atau pengecekan di dashboard.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeknisi(): bool
    {
        return $this->role === 'teknisi';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isKapus(): bool
    {
        return $this->role === 'kapus';
    }

    /**
     * Format Phone Number untuk WhatsApp.
     * Membersihkan karakter non-numeric.
     */
    public function getFormattedPhone(): ?string
    {
        if (! $this->phone_number) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', $this->phone_number);
    }
}

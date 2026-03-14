<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Eager Load otomatis untuk User agar tidak lemot saat menampilkan daftar log.
     */
    protected $with = ['user'];

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'asset_name',
        'action',      // TAMBAH ASET, PERUBAHAN DATA, HAPUS ASET
        'changes',     // Data JSON berisi Before vs After
    ];

    /**
     * Casting atribut agar Laravel mengenali format data dengan benar.
     * Sangat penting agar kolom 'changes' diperlakukan sebagai Array/JSON.
     */
    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * RELASI: Siapa yang melakukan aktivitas ini?
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * SCOPE: Pencarian cepat berdasarkan tindakan tertentu.
     */
    public function scopeOnlyDeletions($query)
    {
        return $query->where('action', 'HAPUS ASET');
    }

    /**
     * HELPER: Mendapatkan inisial user untuk tampilan Avatar di Log.
     */
    public function getUserInitialAttribute(): string
    {
        return strtoupper(substr($this->user?->name ?? 'System', 0, 1));
    }
}

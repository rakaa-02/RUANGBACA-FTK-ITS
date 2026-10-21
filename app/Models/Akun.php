<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Akun mahasiswa/siswa (pengunjung). Dibuat oleh admin. */
class Akun extends Model
{
    protected $table = 'akun';

    const UPDATED_AT = null;

    protected $fillable = ['Nickname', 'Password', 'Fakultas', 'NIM'];
    protected $hidden = ['Password'];

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'id_akun');
    }

    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'id_akun');
    }

    public function scopeCari(Builder $q, ?string $kata): Builder
    {
        if (! $kata) {
            return $q;
        }

        return $q->where(fn ($w) => $w->where('Nickname', 'like', "%{$kata}%")
            ->orWhere('NIM', 'like', "%{$kata}%")
            ->orWhere('Fakultas', 'like', "%{$kata}%"));
    }
}

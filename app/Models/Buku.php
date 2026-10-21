<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'id_buku';
    public $timestamps = false; // tabel lama memakai kolom `tanggal` sebagai tanggal masuk

    protected $fillable = [
        'tanggal', 'judul', 'pengarang', 'kota_terbit', 'penerbit', 'tahun_terbit',
        'no_class', 'no_induk', 'deskripsi_fisik', 'isbn', 'lokasi',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'id_buku');
    }

    public function scopeCari(Builder $q, ?string $kata): Builder
    {
        if (! $kata) {
            return $q;
        }

        return $q->where(fn ($w) => $w->where('judul', 'like', "%{$kata}%")
            ->orWhere('no_induk', 'like', "%{$kata}%")
            ->orWhere('pengarang', 'like', "%{$kata}%")
            ->orWhere('lokasi', 'like', "%{$kata}%"));
    }
}

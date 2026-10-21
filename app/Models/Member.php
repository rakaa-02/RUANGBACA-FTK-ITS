<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    /** Ubah daftar ini sesuai fakultas/departemen di tempat Anda. */
    public const FAKULTAS = [
        'Teknik Perkapalan',
        'Teknik Sistem Perkapalan',
        'Teknik Kelautan',
        'Teknik Transportasi Laut',
        'Teknik Lingkungan',
    ];

    protected $fillable = ['nickname', 'nim', 'fakultas'];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}

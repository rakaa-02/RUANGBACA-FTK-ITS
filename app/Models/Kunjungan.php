<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';
    protected $primaryKey = 'id_kunjungan';

    const UPDATED_AT = null;

    protected $fillable = ['id_akun', 'fakultas', 'tanggal', 'jam'];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'id_akun');
    }
}

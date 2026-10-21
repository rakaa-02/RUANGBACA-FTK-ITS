<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    /** Denda per hari keterlambatan (rupiah). */
    public const DENDA_PER_HARI = 1000;

    protected $fillable = [
        'member_id', 'book_id', 'tgl_pinjam', 'batas_kembali', 'tgl_kembali', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'date',
            'batas_kembali' => 'date',
            'tgl_kembali' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /** Jumlah hari lewat batas (0 bila belum/tidak terlambat). */
    public function getHariTerlambatAttribute(): int
    {
        $batas = $this->batas_kembali->copy()->startOfDay();
        $akhir = ($this->tgl_kembali ?? now())->copy()->startOfDay();

        return max(0, (int) $batas->diffInDays($akhir, false));
    }

    public function getDendaAttribute(): int
    {
        return $this->hari_terlambat * self::DENDA_PER_HARI;
    }

    /** Label tampilan: dipinjam | terlambat | dikembalikan */
    public function getLabelStatusAttribute(): string
    {
        if ($this->status === 'dikembalikan') {
            return 'dikembalikan';
        }

        return $this->hari_terlambat > 0 ? 'terlambat' : 'dipinjam';
    }
}

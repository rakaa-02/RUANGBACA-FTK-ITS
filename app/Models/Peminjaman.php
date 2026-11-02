<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Arti kolom status_peminjaman di database lama:
 *   ya        = sudah dikembalikan
 *   tidak     = belum dikembalikan
 *   terlambat = belum dikembalikan dan lewat batas
 * Aplikasi ini menghitung "terlambat" dari tanggal, jadi tidak bergantung pada nilai tersimpan.
 */
class Peminjaman extends Model
{
    /** Denda per hari keterlambatan (rupiah). */
    public const DENDA_PER_HARI = 1000;

    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_akun', 'id_buku', 'tanggal_pinjam', 'batas_pengembalian',
        'tanggal_dikembalikan', 'status_peminjaman', 'denda',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'batas_pengembalian' => 'date',
            'tanggal_dikembalikan' => 'date',
            'denda' => 'integer',
        ];
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'id_akun');
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'id_buku');
    }

    /** Hanya yang belum dikembalikan. */
    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('status_peminjaman', '!=', 'ya');
    }

    public function getSudahKembaliAttribute(): bool
    {
        return $this->status_peminjaman === 'ya';
    }

    /** Hari lewat batas (sampai hari ini bila belum kembali). */
    public function getHariTerlambatAttribute(): int
    {
        $batas = $this->batas_pengembalian->copy()->startOfDay();
        $akhir = ($this->tanggal_dikembalikan ?? now())->copy()->startOfDay();

        return max(0, (int) $batas->diffInDays($akhir, false));
    }

    /** Denda yang ditampilkan: nilai tersimpan bila sudah kembali, hitung berjalan bila belum. */
    public function getDendaTampilAttribute(): int
    {
        return $this->sudah_kembali ? $this->denda : $this->hari_terlambat * self::DENDA_PER_HARI;
    }

    /** dipinjam | terlambat | dikembalikan */
    public function getLabelAttribute(): string
    {
        if ($this->sudah_kembali) {
            return 'dikembalikan';
        }

        return $this->hari_terlambat > 0 ? 'terlambat' : 'dipinjam';
    }

    /** Sisa hari sampai batas (negatif bila lewat). */
    public function getSisaHariAttribute(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->batas_pengembalian->copy()->startOfDay(), false);
    }

    public function tandaiKembali(): void
    {
        $hari = now()->startOfDay();
        $telat = max(0, (int) $this->batas_pengembalian->copy()->startOfDay()->diffInDays($hari, false));

        $this->update([
            'status_peminjaman' => 'ya',
            'tanggal_dikembalikan' => $hari->toDateString(),
            'denda' => $telat * self::DENDA_PER_HARI,
        ]);
    }
}

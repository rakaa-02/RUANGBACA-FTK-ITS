<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "Buku yang dipinjam": lihat peminjaman berdasarkan tanggal pinjam
 * (hari ini, kemarin, 7/30 hari terakhir, semua, atau rentang tanggal sendiri).
 */
class DipinjamController extends Controller
{
    public const RENTANG = [
        'hari-ini' => 'Hari ini',
        'kemarin' => 'Kemarin',
        '7-hari' => '7 hari terakhir',
        '30-hari' => '30 hari terakhir',
        'semua' => 'Semua',
        'kustom' => 'Pilih tanggal',
    ];

    public function index(Request $request): View
    {
        $request->validate([
            'rentang' => ['nullable', 'in:'.implode(',', array_keys(self::RENTANG))],
            'status' => ['nullable', 'in:semua,belum,terlambat,kembali'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
        ]);

        $rentang = $request->query('rentang', 'hari-ini');
        $status = $request->query('status', 'semua');
        $hariIni = now()->startOfDay();

        [$dari, $sampai] = $this->tanggal($rentang, $request, $hariIni);

        $dasar = fn () => Peminjaman::query()
            ->when($dari, fn ($q) => $q->where('tanggal_pinjam', '>=', $dari))
            ->when($sampai, fn ($q) => $q->where('tanggal_pinjam', '<=', $sampai));

        $ringkas = [
            'total' => $dasar()->count(),
            'belum' => $dasar()->aktif()->count(),
            'terlambat' => $dasar()->aktif()->where('batas_pengembalian', '<', $hariIni->toDateString())->count(),
        ];

        $daftar = $dasar()
            ->with(['akun', 'buku'])
            ->when($status === 'belum', fn ($q) => $q->aktif())
            ->when($status === 'terlambat', fn ($q) => $q->aktif()->where('batas_pengembalian', '<', $hariIni->toDateString()))
            ->when($status === 'kembali', fn ($q) => $q->where('status_peminjaman', 'ya'))
            ->orderByDesc('tanggal_pinjam')->orderByDesc('id_peminjaman')
            ->paginate(15)
            ->withQueryString();

        // jumlah pada chip cepat
        $chip = [
            'hari-ini' => Peminjaman::where('tanggal_pinjam', $hariIni->toDateString())->count(),
            'kemarin' => Peminjaman::where('tanggal_pinjam', $hariIni->copy()->subDay()->toDateString())->count(),
            '7-hari' => Peminjaman::where('tanggal_pinjam', '>=', $hariIni->copy()->subDays(6)->toDateString())->count(),
        ];

        return view('admin.peminjaman.dipinjam', [
            'daftar' => $daftar,
            'ringkas' => $ringkas,
            'rentang' => $rentang,
            'status' => $status,
            'dari' => $dari,
            'sampai' => $sampai,
            'chip' => $chip,
            'labelRentang' => self::RENTANG,
        ]);
    }

    /** @return array{0: ?string, 1: ?string} */
    private function tanggal(string $rentang, Request $request, Carbon $hariIni): array
    {
        return match ($rentang) {
            'hari-ini' => [$hariIni->toDateString(), $hariIni->toDateString()],
            'kemarin' => [$hariIni->copy()->subDay()->toDateString(), $hariIni->copy()->subDay()->toDateString()],
            '7-hari' => [$hariIni->copy()->subDays(6)->toDateString(), $hariIni->toDateString()],
            '30-hari' => [$hariIni->copy()->subDays(29)->toDateString(), $hariIni->toDateString()],
            'kustom' => [
                $request->query('dari') ?: $hariIni->toDateString(),
                $request->query('sampai') ?: $hariIni->toDateString(),
            ],
            default => [null, null], // semua
        };
    }
}

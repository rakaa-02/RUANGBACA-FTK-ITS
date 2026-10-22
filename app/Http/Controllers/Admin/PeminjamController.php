<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Daftar mahasiswa yang meminjam buku: siapa saja, berapa buku, apa yang terlambat.
 */
class PeminjamController extends Controller
{
    public const STATUS = [
        'sedang' => 'Sedang meminjam',
        'terlambat' => 'Terlambat',
        'pernah' => 'Pernah meminjam',
    ];

    public function index(Request $request): View
    {
        $request->validate(['status' => ['nullable', 'in:'.implode(',', array_keys(self::STATUS))]]);

        $status = $request->query('status', 'sedang');
        $today = now()->toDateString();

        $terlambat = fn ($w) => $w->aktif()->where('batas_pengembalian', '<', $today);

        $peminjam = Akun::cari($request->query('q'))
            ->withCount([
                'peminjaman as aktif_count' => fn ($w) => $w->aktif(),
                'peminjaman as terlambat_count' => $terlambat,
                'peminjaman as total_count',
            ])
            // judul buku yang sedang dibawa, untuk ditampilkan langsung di tabel
            ->with(['peminjaman' => fn ($w) => $w->aktif()->with('buku:id_buku,judul')->orderBy('batas_pengembalian')])
            ->when($status === 'sedang', fn ($q) => $q->whereHas('peminjaman', fn ($w) => $w->aktif()))
            ->when($status === 'terlambat', fn ($q) => $q->whereHas('peminjaman', $terlambat))
            ->when($status === 'pernah', fn ($q) => $q->whereHas('peminjaman'))
            ->orderByDesc('aktif_count')->orderBy('Nickname')
            ->paginate(15)
            ->withQueryString();

        $chip = [
            'sedang' => Akun::whereHas('peminjaman', fn ($w) => $w->aktif())->count(),
            'terlambat' => Akun::whereHas('peminjaman', $terlambat)->count(),
            'pernah' => Akun::whereHas('peminjaman')->count(),
        ];

        return view('admin.peminjam.index', [
            'peminjam' => $peminjam,
            'status' => $status,
            'q' => $request->query('q'),
            'chip' => $chip,
            'labelStatus' => self::STATUS,
        ]);
    }

    public function show(Akun $akun): View
    {
        $riwayat = Peminjaman::with(['akun', 'buku'])
            ->where('id_akun', $akun->id)
            ->orderByDesc('tanggal_pinjam')->orderByDesc('id_peminjaman')
            ->paginate(15);

        $ringkas = [
            'total' => $akun->peminjaman()->count(),
            'aktif' => $akun->peminjaman()->aktif()->count(),
            'terlambat' => $akun->peminjaman()->aktif()->where('batas_pengembalian', '<', now()->toDateString())->count(),
        ];

        return view('admin.peminjam.show', compact('akun', 'riwayat', 'ringkas'));
    }
}

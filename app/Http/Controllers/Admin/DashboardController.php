<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kunjungan;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $now = now();
        $today = $now->toDateString();
        $year = (int) $request->input('year', $now->year);
        $years = range($now->year, $now->year - 3);
        if (! in_array($year, $years, true)) {
            $year = $now->year;
        }

        $awalBulan = $now->copy()->startOfMonth()->toDateString();
        $akhirBulan = $now->copy()->endOfMonth()->toDateString();
        $awalTahun = Carbon::create($year, 1, 1)->toDateString();
        $akhirTahun = Carbon::create($year, 12, 31)->toDateString();

        $stats = [
            'pengunjung' => Kunjungan::whereBetween('tanggal', [$awalBulan, $akhirBulan])->count(),
            'peminjaman' => Peminjaman::whereBetween('tanggal_pinjam', [$awalBulan, $akhirBulan])->count(),
            'buku_baru' => Buku::whereBetween('tanggal', [$awalBulan, $akhirBulan])->count(),
            'koleksi' => Buku::count(),
        ];

        // Ringkasan sirkulasi (kartu kedua)
        $sirkulasi = [
            'hari_ini' => Peminjaman::where('tanggal_pinjam', $today)->count(),
            'kemarin' => Peminjaman::where('tanggal_pinjam', $now->copy()->subDay()->toDateString())->count(),
            'belum_kembali' => Peminjaman::aktif()->count(),
            'terlambat' => Peminjaman::aktif()->where('batas_pengembalian', '<', $today)->count(),
        ];

        // Grafik 1: kunjungan harian
        $harian = Kunjungan::selectRaw('tanggal, count(*) as total')
            ->whereBetween('tanggal', [$awalTahun, $akhirTahun])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $chartHarian = [
            'labels' => $harian->map(fn ($r) => Carbon::parse($r->tanggal)->translatedFormat('d M Y'))->all(),
            'data' => $harian->pluck('total')->map(fn ($v) => (int) $v)->all(),
        ];

        // Grafik 2: rata-rata pengunjung per hari aktif, per fakultas
        $fakultas = Kunjungan::selectRaw('fakultas, count(*) as total, count(distinct tanggal) as hari')
            ->whereBetween('tanggal', [$awalTahun, $akhirTahun])
            ->groupBy('fakultas')
            ->get()
            ->map(fn ($r) => [
                'fakultas' => $r->fakultas,
                'rata' => round($r->total / max(1, $r->hari), 1),
                'total' => (int) $r->total,
            ])
            ->sortByDesc('rata')
            ->values();

        $chartFakultas = [
            'labels' => $fakultas->pluck('fakultas')->all(),
            'data' => $fakultas->pluck('rata')->all(),
            'total' => $fakultas->pluck('total')->all(),
        ];

        // Grafik 3: buku baru per bulan (kolom `tanggal` di tabel buku)
        $perBulan = Buku::whereBetween('tanggal', [$awalTahun, $akhirTahun])
            ->pluck('tanggal')
            ->map(fn ($t) => Carbon::parse($t)->month)
            ->countBy();

        $bulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $bulan[] = (int) ($perBulan[$m] ?? 0);
        }
        $chartBuku = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'data' => $bulan,
        ];

        return view('admin.dashboard', compact('stats', 'sirkulasi', 'year', 'years', 'chartHarian', 'chartFakultas', 'chartBuku'));
    }
}

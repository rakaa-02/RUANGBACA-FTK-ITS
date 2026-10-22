<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
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
        $kemarin = $now->copy()->subDay()->toDateString();

        $year = (int) $request->input('year', $now->year);
        $years = range($now->year, $now->year - 3);
        if (! in_array($year, $years, true)) {
            $year = $now->year;
        }

        $awalBulan = $now->copy()->startOfMonth()->toDateString();
        $akhirBulan = $now->copy()->endOfMonth()->toDateString();
        $awalTahun = Carbon::create($year, 1, 1)->toDateString();
        $akhirTahun = Carbon::create($year, 12, 31)->toDateString();

        // Kartu ringkasan
        $stats = [
            'peminjaman' => Peminjaman::whereBetween('tanggal_pinjam', [$awalBulan, $akhirBulan])->count(),
            'buku_baru' => Buku::whereBetween('tanggal', [$awalBulan, $akhirBulan])->count(),
            'koleksi' => Buku::count(),
            'dipinjam_hari_ini' => Peminjaman::where('tanggal_pinjam', $today)->count(),
            'dipinjam_kemarin' => Peminjaman::where('tanggal_pinjam', $kemarin)->count(),
            'belum_kembali' => Peminjaman::aktif()->count(),
            'terlambat' => Peminjaman::aktif()->where('batas_pengembalian', '<', $today)->count(),
        ];

        // Grafik 1: jumlah peminjaman per hari (hari yang ada peminjaman)
        $harian = Peminjaman::selectRaw('tanggal_pinjam as tanggal, count(*) as total')
            ->whereBetween('tanggal_pinjam', [$awalTahun, $akhirTahun])
            ->groupBy('tanggal_pinjam')
            ->orderBy('tanggal_pinjam')
            ->get();

        $chartHarian = [
            'labels' => $harian->map(fn ($r) => Carbon::parse($r->tanggal)->translatedFormat('d M Y'))->all(),
            'data' => $harian->pluck('total')->map(fn ($v) => (int) $v)->all(),
        ];

        // Grafik 2: jumlah peminjaman per fakultas/departemen (dari akun peminjam)
        $fakultas = Peminjaman::join('akun', 'akun.id', '=', 'peminjaman.id_akun')
            ->whereBetween('peminjaman.tanggal_pinjam', [$awalTahun, $akhirTahun])
            ->selectRaw('akun.Fakultas as fakultas, count(*) as total')
            ->groupBy('akun.Fakultas')
            ->orderByDesc('total')
            ->get();

        $chartFakultas = [
            'labels' => $fakultas->pluck('fakultas')->all(),
            'data' => $fakultas->pluck('total')->map(fn ($v) => (int) $v)->all(),
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

        return view('admin.dashboard', compact('stats', 'year', 'years', 'chartHarian', 'chartFakultas', 'chartBuku'));
    }
}

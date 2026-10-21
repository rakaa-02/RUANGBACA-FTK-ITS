<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $now = now();
        $year = (int) $request->input('year', $now->year);
        $years = range($now->year, $now->year - 3);
        if (! in_array($year, $years, true)) {
            $year = $now->year;
        }

        $awalBulan = $now->copy()->startOfMonth();
        $akhirBulan = $now->copy()->endOfMonth();
        $awalTahun = Carbon::create($year, 1, 1)->startOfDay();
        $akhirTahun = Carbon::create($year, 12, 31)->endOfDay();

        // Kartu ringkasan
        $stats = [
            'pengunjung' => Visit::whereBetween('tanggal', [$awalBulan->toDateString(), $akhirBulan->toDateString()])->count(),
            'peminjaman' => Loan::whereBetween('tgl_pinjam', [$awalBulan->toDateString(), $akhirBulan->toDateString()])->count(),
            'buku_baru' => Book::whereBetween('created_at', [$awalBulan, $akhirBulan])->count(),
            'koleksi' => Book::count(),
        ];

        // Grafik 1: kunjungan harian (hari yang ada kunjungannya)
        $harian = Visit::selectRaw('tanggal, count(*) as total')
            ->whereBetween('tanggal', [$awalTahun->toDateString(), $akhirTahun->toDateString()])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $chartHarian = [
            'labels' => $harian->map(fn ($r) => Carbon::parse($r->tanggal)->translatedFormat('d M Y'))->all(),
            'data' => $harian->pluck('total')->map(fn ($v) => (int) $v)->all(),
        ];

        // Grafik 2: rata-rata pengunjung per hari aktif, per fakultas
        $fakultas = Visit::selectRaw('fakultas, count(*) as total, count(distinct tanggal) as hari')
            ->whereBetween('tanggal', [$awalTahun->toDateString(), $akhirTahun->toDateString()])
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

        // Grafik 3: buku baru per bulan
        $perBulan = Book::whereBetween('created_at', [$awalTahun, $akhirTahun])
            ->get(['created_at'])
            ->groupBy(fn ($b) => (int) $b->created_at->format('n'))
            ->map->count();

        $bulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $bulan[] = (int) ($perBulan[$m] ?? 0);
        }
        $chartBuku = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'data' => $bulan,
        ];

        return view('dashboard', compact('stats', 'year', 'years', 'chartHarian', 'chartFakultas', 'chartBuku'));
    }
}

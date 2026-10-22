<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Halaman khusus mahasiswa/siswa: hanya melihat peminjaman miliknya sendiri. */
class BerandaController extends Controller
{
    public function index(Request $request): View
    {
        $akun = Akun::findOrFail($request->session()->get('uid'));

        $sedang = Peminjaman::with('buku')
            ->where('id_akun', $akun->id)
            ->aktif()
            ->orderBy('batas_pengembalian')
            ->get();

        $riwayat = Peminjaman::with('buku')
            ->where('id_akun', $akun->id)
            ->where('status_peminjaman', 'ya')
            ->orderByDesc('tanggal_pinjam')->orderByDesc('id_peminjaman')
            ->paginate(8);

        return view('visitor.beranda', compact('akun', 'sedang', 'riwayat'));
    }
}

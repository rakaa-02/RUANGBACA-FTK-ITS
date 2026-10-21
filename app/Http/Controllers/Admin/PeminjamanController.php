<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(): View
    {
        return view('admin.peminjaman.index', [
            'akun' => Akun::orderBy('Nickname')->get(['id', 'Nickname']),
            'peminjaman' => Peminjaman::with(['akun', 'buku'])
                ->orderByDesc('tanggal_pinjam')->orderByDesc('id_peminjaman')
                ->paginate(10),
            'tglPinjam' => now()->toDateString(),
            'tglBatas' => now()->addDays(14)->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_akun' => ['required', 'exists:akun,id'],
            'id_buku' => ['required', 'exists:buku,id_buku'],
            'tanggal_pinjam' => ['required', 'date'],
            'batas_pengembalian' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'status_peminjaman' => ['required', 'in:ya,tidak'],
        ], [
            'id_akun.required' => 'Pilih nickname peminjam.',
            'id_buku.required' => 'Pilih buku dari daftar saran yang muncul saat mengetik.',
            'batas_pengembalian.after_or_equal' => 'Batas pengembalian tidak boleh sebelum tanggal pinjam.',
            'status_peminjaman.required' => 'Pilih status peminjaman.',
        ]);

        // satu eksemplar (satu baris buku) tidak bisa dipinjam dua orang sekaligus
        if ($data['status_peminjaman'] === 'tidak'
            && Peminjaman::aktif()->where('id_buku', $data['id_buku'])->exists()) {
            return back()->withInput()->withErrors(['id_buku' => 'Buku ini sedang dipinjam dan belum dikembalikan.']);
        }

        $pinjam = Peminjaman::create([
            'id_akun' => $data['id_akun'],
            'id_buku' => $data['id_buku'],
            'tanggal_pinjam' => $data['tanggal_pinjam'],
            'batas_pengembalian' => $data['batas_pengembalian'],
            'status_peminjaman' => 'tidak',
            'denda' => 0,
        ]);

        if ($data['status_peminjaman'] === 'ya') {
            $pinjam->tandaiKembali();
        }

        return redirect()->route('admin.peminjaman.index')->with('sukses', 'Peminjaman tersimpan.');
    }

    public function kembalikan(Peminjaman $peminjaman): RedirectResponse
    {
        if (! $peminjaman->sudah_kembali) {
            $peminjaman->tandaiKembali();
        }

        return back()->with('sukses', 'Buku ditandai sudah dikembalikan.');
    }

    public function destroy(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->delete();

        return back()->with('sukses', 'Catatan peminjaman dihapus.');
    }
}

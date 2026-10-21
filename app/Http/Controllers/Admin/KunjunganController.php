<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KunjunganController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['tanggal' => ['nullable', 'date']]);
        $tanggal = $request->query('tanggal');

        $kunjungan = Kunjungan::with('akun')
            ->when($tanggal, fn ($q) => $q->where('tanggal', $tanggal))
            ->orderByDesc('tanggal')->orderByDesc('jam')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pengunjung.index', compact('kunjungan', 'tanggal'));
    }

    public function destroy(Kunjungan $kunjungan): RedirectResponse
    {
        $kunjungan->delete();

        return back()->with('sukses', 'Catatan kunjungan dihapus.');
    }
}

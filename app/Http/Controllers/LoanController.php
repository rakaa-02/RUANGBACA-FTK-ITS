<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(): View
    {
        return view('loans.index', [
            'members' => Member::orderBy('nickname')->get(['id', 'nickname']),
            'loans' => Loan::with(['member', 'book'])->latest('tgl_pinjam')->latest('id')->paginate(10),
            'defaultPinjam' => now()->toDateString(),
            'defaultBatas' => now()->addDays(7)->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'book_id' => ['required', 'exists:books,id'],
            'tgl_pinjam' => ['required', 'date'],
            'batas_kembali' => ['required', 'date', 'after_or_equal:tgl_pinjam'],
            'status' => ['required', 'in:dipinjam,dikembalikan'],
        ], [
            'book_id.required' => 'Pilih buku dari daftar saran yang muncul saat mengetik.',
            'batas_kembali.after_or_equal' => 'Batas pengembalian tidak boleh sebelum tanggal pinjam.',
        ]);

        $data['tgl_kembali'] = $data['status'] === 'dikembalikan' ? now()->toDateString() : null;

        Loan::create($data);

        return redirect()->route('loans.index')->with('success', 'Peminjaman tersimpan.');
    }

    public function kembalikan(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'dikembalikan') {
            $loan->update(['status' => 'dikembalikan', 'tgl_kembali' => now()->toDateString()]);
        }

        return back()->with('success', 'Buku ditandai sudah dikembalikan.');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $loan->delete();

        return back()->with('success', 'Catatan peminjaman dihapus.');
    }
}

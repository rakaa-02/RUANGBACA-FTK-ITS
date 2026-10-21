<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VisitController extends Controller
{
    /** Halaman publik: pengunjung mengisi sendiri saat datang. */
    public function create(): View
    {
        return view('visits.checkin', ['fakultas' => Member::FAKULTAS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nickname' => ['required', 'string', 'max:100'],
            'nim' => ['required', 'string', 'max:30'],
            'fakultas' => ['required', Rule::in(Member::FAKULTAS)],
        ]);

        $member = Member::updateOrCreate(
            ['nim' => $data['nim']],
            ['nickname' => $data['nickname'], 'fakultas' => $data['fakultas']]
        );

        $visit = Visit::firstOrCreate(
            ['member_id' => $member->id, 'tanggal' => now()->toDateString()],
            ['fakultas' => $member->fakultas, 'jam' => now()->format('H:i:s')]
        );

        return redirect()->route('checkin')->with(
            $visit->wasRecentlyCreated ? 'success' : 'info',
            $visit->wasRecentlyCreated
                ? "Selamat datang, {$member->nickname}. Kunjungan Anda sudah tercatat."
                : "{$member->nickname}, kunjungan Anda hari ini sudah tercatat sebelumnya."
        );
    }

    /** Halaman admin: tabel pengunjung. */
    public function index(Request $request): View
    {
        $tanggal = $request->query('tanggal');

        $visits = Visit::with('member')
            ->when($tanggal, fn ($q) => $q->whereDate('tanggal', $tanggal))
            ->latest('tanggal')->latest('jam')
            ->paginate(15)
            ->withQueryString();

        return view('visits.index', compact('visits', 'tanggal'));
    }

    public function destroy(Visit $visit): RedirectResponse
    {
        $visit->delete();

        return back()->with('success', 'Catatan kunjungan dihapus.');
    }
}

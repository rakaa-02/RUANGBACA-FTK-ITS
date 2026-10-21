<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Admin mengelola akun mahasiswa/siswa (tabel `akun`). */
class AnggotaController extends Controller
{
    public function index(Request $request): View
    {
        $anggota = Akun::cari($request->query('q'))
            ->withCount(['peminjaman as aktif_count' => fn ($q) => $q->aktif()])
            ->orderBy('Nickname')
            ->paginate(10)
            ->withQueryString();

        return view('admin.anggota.index', ['anggota' => $anggota, 'q' => $request->query('q')]);
    }

    public function create(): View
    {
        return view('admin.anggota.form', ['akun' => new Akun, 'fakultas' => $this->daftarFakultas()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['Password'] = Hash::make($data['Password']);

        Akun::create($data);

        return redirect()->route('admin.anggota.index')->with('sukses', 'Akun mahasiswa dibuat.');
    }

    public function edit(Akun $akun): View
    {
        return view('admin.anggota.form', ['akun' => $akun, 'fakultas' => $this->daftarFakultas()]);
    }

    public function update(Request $request, Akun $akun): RedirectResponse
    {
        $data = $this->validasi($request, $akun);

        if (filled($data['Password'] ?? null)) {
            $data['Password'] = Hash::make($data['Password']);
        } else {
            unset($data['Password']); // kosong = password tidak diubah
        }

        $akun->update($data);

        return redirect()->route('admin.anggota.index')->with('sukses', 'Akun diperbarui.');
    }

    public function destroy(Akun $akun): RedirectResponse
    {
        if ($akun->peminjaman()->aktif()->exists()) {
            return back()->with('galat', 'Akun ini masih punya buku yang belum dikembalikan.');
        }

        // FK peminjaman→akun bersifat CASCADE: riwayat peminjaman akun ini ikut terhapus
        $akun->delete();

        return back()->with('sukses', 'Akun dihapus.');
    }

    private function daftarFakultas()
    {
        return Akun::query()->distinct()->orderBy('Fakultas')->pluck('Fakultas');
    }

    private function validasi(Request $request, ?Akun $akun = null): array
    {
        return $request->validate([
            'Nickname' => ['required', 'string', 'max:50', Rule::unique('akun', 'Nickname')->ignore($akun?->id)],
            'NIM' => ['required', 'string', 'max:20', Rule::unique('akun', 'NIM')->ignore($akun?->id)],
            'Fakultas' => ['required', 'string', 'max:100'],
            'Password' => [$akun ? 'nullable' : 'required', 'string', 'min:6', 'max:100'],
        ], [
            'Nickname.unique' => 'Nickname ini sudah dipakai.',
            'NIM.unique' => 'NIM ini sudah terdaftar.',
            'Password.min' => 'Password minimal 6 karakter.',
        ]);
    }
}

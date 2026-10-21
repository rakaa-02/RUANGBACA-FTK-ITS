<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Services\XlsxExporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $buku = Buku::cari($request->query('q'))
            ->orderByDesc('id_buku')
            ->paginate(10)
            ->withQueryString();

        return view('admin.buku.index', ['buku' => $buku, 'q' => $request->query('q')]);
    }

    public function create(): View
    {
        return view('admin.buku.form', ['buku' => new Buku(['tanggal' => now()->toDateString()])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Buku::create($this->validasi($request));

        return redirect()->route('admin.buku.index')->with('sukses', 'Buku berhasil ditambahkan.');
    }

    public function edit(Buku $buku): View
    {
        return view('admin.buku.form', compact('buku'));
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $buku->update($this->validasi($request));

        return redirect()->route('admin.buku.index')->with('sukses', 'Data buku diperbarui.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        // FK peminjaman→buku tanpa cascade: buku yang pernah dipinjam tidak boleh dihapus
        if ($buku->peminjaman()->exists()) {
            return back()->with('galat', 'Buku ini punya riwayat peminjaman, jadi tidak bisa dihapus.');
        }

        $buku->delete();

        return back()->with('sukses', 'Buku dihapus.');
    }

    /** Autocomplete kolom "Judul Buku" pada form peminjaman. */
    public function cari(Request $request): JsonResponse
    {
        $hasil = Buku::select(['id_buku', 'judul', 'pengarang', 'no_induk', 'lokasi'])
            ->cari($request->query('q'))
            ->withExists(['peminjaman as dipinjam' => fn ($q) => $q->aktif()])
            ->orderBy('judul')
            ->limit(8)
            ->get();

        return response()->json($hasil);
    }

    public function export(): BinaryFileResponse
    {
        $baris = Buku::orderBy('id_buku')->cursor()->map(fn ($b) => [
            $b->id_buku,
            $b->tanggal?->format('Y-m-d') ?? '',
            $b->judul, $b->pengarang, $b->kota_terbit, $b->penerbit,
            (int) $b->tahun_terbit,
            $b->no_class, $b->no_induk, $b->deskripsi_fisik, $b->isbn, $b->lokasi,
        ]);

        $path = XlsxExporter::make('Daftar Buku', [
            'ID', 'Tanggal Masuk', 'Judul', 'Pengarang', 'Kota Terbit', 'Penerbit', 'Tahun',
            'No Class', 'No Induk', 'Deskripsi Fisik', 'ISBN', 'Lokasi',
        ], $baris);

        return response()
            ->download($path, 'daftar-buku-'.now()->format('Ymd').'.xlsx')
            ->deleteFileAfterSend(true);
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'tanggal' => ['required', 'date'],
            'judul' => ['required', 'string', 'max:255'],
            'pengarang' => ['nullable', 'string', 'max:255'],
            'kota_terbit' => ['nullable', 'string', 'max:255'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            // kolom YEAR di MySQL hanya menerima 0 atau 1901–2155
            'tahun_terbit' => ['required', 'integer', function ($atribut, $nilai, $gagal) {
                if ((int) $nilai !== 0 && ($nilai < 1901 || $nilai > 2155)) {
                    $gagal('Tahun terbit harus 0 (tidak diketahui) atau antara 1901 dan 2155.');
                }
            }],
            'no_class' => ['nullable', 'string', 'max:255'],
            'no_induk' => ['required', 'string', 'max:255'],
            'deskripsi_fisik' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);
    }
}

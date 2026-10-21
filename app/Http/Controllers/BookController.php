<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\XlsxExporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::search($request->query('q'))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('books.index', ['books' => $books, 'q' => $request->query('q')]);
    }

    public function create(): View
    {
        return view('books.form', ['book' => new Book]);
    }

    public function store(Request $request): RedirectResponse
    {
        Book::create($this->validated($request));

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        return view('books.form', compact('book'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $book->update($this->validated($request, $book));

        return redirect()->route('books.index')->with('success', 'Data buku diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return back()->with('success', 'Buku dihapus.');
    }

    /** Pencarian untuk kolom "Judul Buku" di form peminjaman (autocomplete). */
    public function search(Request $request): JsonResponse
    {
        $books = Book::search($request->query('q'))
            ->orderBy('judul')
            ->limit(8)
            ->get(['id', 'judul', 'no_induk', 'lokasi']);

        return response()->json($books);
    }

    public function export(): BinaryFileResponse
    {
        $rows = Book::orderBy('id')->cursor()->map(fn ($b, $i) => [
            $b->id, $b->judul, $b->no_induk, $b->lokasi,
        ]);

        $path = XlsxExporter::make('Daftar Buku', ['ID', 'Judul Buku', 'No Induk', 'Lokasi'], $rows);

        return response()
            ->download($path, 'daftar-buku-'.now()->format('Ymd').'.xlsx')
            ->deleteFileAfterSend(true);
    }

    private function validated(Request $request, ?Book $book = null): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'no_induk' => ['required', 'string', 'max:50', Rule::unique('books', 'no_induk')->ignore($book?->id)],
            'lokasi' => ['required', 'string', 'max:50'],
        ]);
    }
}

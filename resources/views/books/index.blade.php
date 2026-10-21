@extends('layouts.app')

@section('title', 'Pendataan Buku')
@section('kicker', 'Koleksi')
@section('heading', 'Daftar Buku')
@section('lead', 'Kelola judul, nomor induk, dan lokasi rak seluruh koleksi.')

@section('content')
<div class="card">
    <div class="toolbar">
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a href="{{ route('books.create') }}" class="btn">+ Tambah Buku Baru</a>
            <a href="{{ route('books.export') }}" class="btn ghost">Ekspor ke Excel</a>
        </div>
        <form class="search" method="GET" action="{{ route('books.index') }}">
            <input class="input" type="search" name="q" value="{{ $q }}" placeholder="Cari judul, no induk, lokasi…">
            <button class="btn copper" type="submit">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        <table class="ledger">
            <thead>
            <tr><th>No</th><th>Judul Buku</th><th>No Induk</th><th>Lokasi</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse ($books as $book)
                <tr>
                    <td class="num">{{ $books->firstItem() + $loop->index }}</td>
                    <td class="title">{{ $book->judul }}</td>
                    <td class="mono">{{ $book->no_induk }}</td>
                    <td class="mono">{{ $book->lokasi }}</td>
                    <td>
                        <div class="actions">
                            <a class="btn sm ghost" href="{{ route('books.edit', $book) }}">Edit</a>
                            <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Hapus buku ini?')">
                                @csrf @method('DELETE')
                                <button class="btn sm danger" type="submit">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Tidak ada buku yang cocok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $books->links() }}
</div>
@endsection

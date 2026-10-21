@extends('layouts.admin')

@section('title', 'Pendataan Buku')
@section('heading', 'Dashboard - Daftar Buku')

@section('content')
<div class="card">
    <div class="toolbar">
        <div class="left">
            <a href="{{ route('admin.buku.create') }}" class="btn">+ Tambah Buku Baru</a>
            <a href="{{ route('admin.buku.export') }}" class="btn green">Export ke Excel</a>
        </div>
        <form class="search" method="GET" action="{{ route('admin.buku.index') }}">
            <input class="input" type="search" name="q" value="{{ $q }}" placeholder="Cari judul, no induk, lokasi...">
            <button class="btn sky" type="submit">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        <table class="tbl">
            <thead>
            <tr><th>No</th><th>Judul Buku</th><th>No Induk</th><th>Lokasi</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse ($buku as $b)
                <tr>
                    <td>{{ $buku->firstItem() + $loop->index }}</td>
                    <td>
                        {{ $b->judul }}
                        @if ($b->pengarang)<small>{{ $b->pengarang }}</small>@endif
                    </td>
                    <td class="nowrap">{{ $b->no_induk }}</td>
                    <td class="nowrap">{{ $b->lokasi }}</td>
                    <td>
                        <div class="aksi">
                            <a class="btn sm warn" href="{{ route('admin.buku.edit', $b) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.buku.destroy', $b) }}" onsubmit="return confirm('Hapus buku ini?')">
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

    {{ $buku->links() }}
</div>
@endsection

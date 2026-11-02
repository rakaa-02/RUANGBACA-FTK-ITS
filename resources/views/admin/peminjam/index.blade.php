@extends('layouts.admin')

@section('title', 'Daftar Peminjam')
@section('heading', 'Daftar Peminjam')
@section('lead', 'Mahasiswa yang meminjam buku, lengkap dengan buku yang sedang mereka bawa.')

@section('content')
<div class="card">
    <div class="toolbar">
        <div class="chips">
            @foreach ($labelStatus as $kode => $label)
                <a class="chip {{ $status === $kode ? 'on' : '' }}"
                   href="{{ route('admin.peminjam.index', ['status' => $kode, 'q' => $q]) }}">
                    {{ $label }}<em>{{ $chip[$kode] }}</em>
                </a>
            @endforeach
        </div>

        <form class="search" method="GET" action="{{ route('admin.peminjam.index') }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <input class="input" type="search" name="q" value="{{ $q }}" placeholder="Cari nickname, NIM, fakultas...">
            <button class="btn sky" type="submit">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        <table class="tbl">
            <thead>
            <tr><th>No</th><th>Mahasiswa</th><th>Fakultas</th><th>Buku yang Sedang Dipinjam</th><th>Terlambat</th><th>Total Pinjam</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse ($peminjam as $m)
                <tr>
                    <td>{{ $peminjam->firstItem() + $loop->index }}</td>
                    <td>{{ $m->Nickname }}<small>{{ $m->NIM }}</small></td>
                    <td>{{ $m->Fakultas }}</td>
                    <td>
                        @forelse ($m->peminjaman as $p)
                            <div>
                                {{ \Illuminate\Support\Str::limit($p->buku->judul, 60) }}
                                <small>batas {{ $p->batas_pengembalian->translatedFormat('d M Y') }}</small>
                            </div>
                        @empty
                            <span style="color:var(--muted)">-</span>
                        @endforelse
                    </td>
                    <td>
                        @if ($m->terlambat_count)<span class="badge terlambat">{{ $m->terlambat_count }} buku</span>@else - @endif
                    </td>
                    <td>{{ $m->total_count }}</td>
                    <td><a class="btn sm" href="{{ route('admin.peminjam.show', $m) }}">Lihat detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Tidak ada mahasiswa pada kategori ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $peminjam->links() }}
</div>
@endsection

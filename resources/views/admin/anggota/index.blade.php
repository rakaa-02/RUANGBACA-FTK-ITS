@extends('layouts.admin')

@section('title', 'Akun Mahasiswa')
@section('heading', 'Akun Mahasiswa')
@section('lead', 'Akun dibuat oleh admin. Mahasiswa memakai nickname dan password ini untuk login.')

@section('content')
<div class="card">
    <div class="toolbar">
        <div class="left">
            <a href="{{ route('admin.anggota.create') }}" class="btn">+ Tambah Akun</a>
        </div>
        <form class="search" method="GET" action="{{ route('admin.anggota.index') }}">
            <input class="input" type="search" name="q" value="{{ $q }}" placeholder="Cari nickname, NIM, fakultas...">
            <button class="btn sky" type="submit">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        <table class="tbl">
            <thead>
            <tr><th>No</th><th>Nickname</th><th>NIM</th><th>Fakultas</th><th>Pinjaman Aktif</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse ($anggota as $a)
                <tr>
                    <td>{{ $anggota->firstItem() + $loop->index }}</td>
                    <td>{{ $a->Nickname }}</td>
                    <td class="nowrap">{{ $a->NIM }}</td>
                    <td>{{ $a->Fakultas }}</td>
                    <td>{{ $a->aktif_count ?: '-' }}</td>
                    <td>
                        <div class="aksi">
                            <a class="btn sm warn" href="{{ route('admin.anggota.edit', $a) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.anggota.destroy', $a) }}"
                                  onsubmit="return confirm('Hapus akun ini? Riwayat peminjamannya ikut terhapus.')">
                                @csrf @method('DELETE')
                                <button class="btn sm danger" type="submit">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada akun.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $anggota->links() }}
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Data Pengunjung')
@section('heading', 'Data Pengunjung')
@section('lead', 'Kunjungan tercatat otomatis saat mahasiswa login (satu kali per hari). Data ini menjadi sumber grafik dashboard.')

@section('content')
<div class="card">
    <div class="toolbar">
        <form class="search" method="GET" action="{{ route('admin.pengunjung.index') }}">
            <input class="input" type="date" name="tanggal" value="{{ $tanggal }}" style="width:auto">
            <button class="btn sky" type="submit">Filter</button>
        </form>
        @if ($tanggal)<a class="btn sm ghost" href="{{ route('admin.pengunjung.index') }}">Hapus filter</a>@endif
    </div>

    <div class="table-wrap">
        <table class="tbl">
            <thead>
            <tr><th>No</th><th>Nickname</th><th>NIM</th><th>Fakultas</th><th>Tanggal</th><th>Jam</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse ($kunjungan as $k)
                <tr>
                    <td>{{ $kunjungan->firstItem() + $loop->index }}</td>
                    <td>{{ $k->akun->Nickname }}</td>
                    <td class="nowrap">{{ $k->akun->NIM }}</td>
                    <td>{{ $k->fakultas }}</td>
                    <td class="nowrap">{{ $k->tanggal->translatedFormat('d M Y') }}</td>
                    <td class="nowrap">{{ substr($k->jam, 0, 5) }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.pengunjung.destroy', $k) }}" onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn sm danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Belum ada catatan kunjungan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $kunjungan->links() }}
</div>
@endsection

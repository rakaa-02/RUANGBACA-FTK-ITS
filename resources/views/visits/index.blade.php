@extends('layouts.app')

@section('title', 'Pengunjung')
@section('kicker', 'Daftar hadir')
@section('heading', 'Data Pengunjung')
@section('lead', 'Catatan kehadiran dari halaman check-in. Data ini menjadi sumber grafik di dashboard.')

@section('content')
<div class="card">
    <div class="toolbar">
        <form class="search" method="GET" action="{{ route('visits.index') }}">
            <input class="input" type="date" name="tanggal" value="{{ $tanggal }}">
            <button class="btn copper" type="submit">Filter</button>
        </form>
        @if ($tanggal)<a class="btn sm ghost" href="{{ route('visits.index') }}">Hapus filter</a>@endif
    </div>

    <div class="table-wrap">
        <table class="ledger">
            <thead>
            <tr><th>No</th><th>Nama</th><th>NIM</th><th>Fakultas</th><th>Tanggal</th><th>Jam</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse ($visits as $v)
                <tr>
                    <td class="num">{{ $visits->firstItem() + $loop->index }}</td>
                    <td class="title">{{ $v->member->nickname }}</td>
                    <td class="mono">{{ $v->member->nim }}</td>
                    <td>{{ $v->fakultas }}</td>
                    <td class="mono">{{ $v->tanggal->translatedFormat('d M Y') }}</td>
                    <td class="mono">{{ substr($v->jam, 0, 5) }}</td>
                    <td>
                        <form method="POST" action="{{ route('visits.destroy', $v) }}" onsubmit="return confirm('Hapus catatan ini?')">
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

    {{ $visits->links() }}
</div>
@endsection

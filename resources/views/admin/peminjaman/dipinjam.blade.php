@extends('layouts.admin')

@section('title', 'Buku Dipinjam')
@section('heading', 'Buku yang Dipinjam')
@section('lead', 'Lihat buku apa saja yang dipinjam hari ini, kemarin, atau pada rentang tanggal tertentu.')

@section('content')
<div class="card">
    {{-- Pilihan rentang berdasarkan tanggal pinjam --}}
    <div class="chips">
        @foreach (['hari-ini', 'kemarin', '7-hari', '30-hari', 'semua'] as $r)
            <a class="chip {{ $rentang === $r ? 'on' : '' }}"
               href="{{ route('admin.dipinjam', ['rentang' => $r, 'status' => $status]) }}">
                {{ $labelRentang[$r] }}@if (isset($chip[$r]))<em>{{ $chip[$r] }}</em>@endif
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.dipinjam') }}" class="filters">
        <input type="hidden" name="rentang" value="kustom">
        <div class="field">
            <label for="dari">Dari tanggal</label>
            <input class="input" type="date" id="dari" name="dari" value="{{ $rentang === 'kustom' ? $dari : '' }}">
        </div>
        <div class="field">
            <label for="sampai">Sampai tanggal</label>
            <input class="input" type="date" id="sampai" name="sampai" value="{{ $rentang === 'kustom' ? $sampai : '' }}">
        </div>
        <div class="field">
            <label for="status">Status</label>
            <select class="input" id="status" name="status">
                <option value="semua" @selected($status === 'semua')>Semua</option>
                <option value="belum" @selected($status === 'belum')>Belum dikembalikan</option>
                <option value="terlambat" @selected($status === 'terlambat')>Terlambat</option>
                <option value="kembali" @selected($status === 'kembali')>Sudah dikembalikan</option>
            </select>
        </div>
        <button class="btn sky" type="submit">Terapkan</button>
    </form>
</div>

<div class="grid g3 mt">
    <div class="card stat">
        <div><small>Total dipinjam ({{ $labelRentang[$rentang] }})</small><strong>{{ $ringkas['total'] }}</strong></div>
        <span class="ico">@include('partials.icons', ['nama' => 'buku'])</span>
    </div>
    <div class="card stat">
        <div><small>Belum dikembalikan</small><strong>{{ $ringkas['belum'] }}</strong></div>
        <span class="ico amber">@include('partials.icons', ['nama' => 'pinjam'])</span>
    </div>
    <div class="card stat">
        <div><small>Terlambat</small><strong>{{ $ringkas['terlambat'] }}</strong></div>
        <span class="ico red">@include('partials.icons', ['nama' => 'awas'])</span>
    </div>
</div>

<div class="card mt">
    <div class="card-head">
        <h2>
            Daftar Peminjaman
            @if ($dari && $sampai)
                @if ($dari === $sampai)
                    <span style="font-weight:400"> · {{ \Carbon\Carbon::parse($dari)->translatedFormat('l, d F Y') }}</span>
                @else
                    <span style="font-weight:400"> · {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</span>
                @endif
            @endif
        </h2>
        <span>Tanggal mengacu pada tanggal pinjam</span>
    </div>

    @include('admin.peminjaman._tabel', ['daftar' => $daftar])
    {{ $daftar->links() }}
</div>
@endsection

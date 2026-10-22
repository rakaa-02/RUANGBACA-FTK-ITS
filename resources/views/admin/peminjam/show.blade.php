@extends('layouts.admin')

@section('title', 'Detail Peminjam')
@section('heading', $akun->Nickname)
@section('lead', $akun->Fakultas.' · NIM '.$akun->NIM)

@section('content')
<div class="toolbar">
    <a class="btn ghost" href="{{ route('admin.peminjam.index') }}">← Kembali ke daftar peminjam</a>
</div>

<div class="grid g3">
    <div class="card stat">
        <div><small>Total peminjaman</small><strong>{{ $ringkas['total'] }}</strong></div>
        <span class="ico">@include('partials.icons', ['nama' => 'buku'])</span>
    </div>
    <div class="card stat">
        <div><small>Sedang dipinjam</small><strong>{{ $ringkas['aktif'] }}</strong></div>
        <span class="ico amber">@include('partials.icons', ['nama' => 'pinjam'])</span>
    </div>
    <div class="card stat">
        <div><small>Terlambat</small><strong>{{ $ringkas['terlambat'] }}</strong></div>
        <span class="ico red">@include('partials.icons', ['nama' => 'awas'])</span>
    </div>
</div>

<div class="card mt">
    <div class="card-head"><h2>Riwayat Peminjaman</h2><span>Terbaru di atas</span></div>
    @include('admin.peminjaman._tabel', ['daftar' => $riwayat])
    {{ $riwayat->links() }}
</div>
@endsection

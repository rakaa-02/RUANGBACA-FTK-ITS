@extends('layouts.visitor')

@section('title', 'Beranda')

@section('content')
<section class="hello">
    <h1>Halo, {{ $akun->Nickname }}</h1>
    <p>{{ $akun->Fakultas }} · NIM {{ $akun->NIM }}</p>
</section>

<div class="card-head"><h2>Buku yang Sedang Anda Pinjam</h2></div>

@if ($sedang->isEmpty())
    <div class="kosong">
        <strong>Belum ada peminjaman buku</strong>
        Saat Anda meminjam buku, judul dan batas pengembaliannya akan muncul di sini.
    </div>
@else
    <div class="pinjam-grid">
        @foreach ($sedang as $p)
            <article class="pinjam {{ $p->label }}">
                <h3>{{ $p->buku->judul }}</h3>
                <div class="pengarang">{{ $p->buku->pengarang ?: 'Pengarang tidak tercatat' }}</div>

                <dl>
                    <dt>Dipinjam</dt><dd>{{ $p->tanggal_pinjam->translatedFormat('d M Y') }}</dd>
                    <dt>Batas kembali</dt><dd>{{ $p->batas_pengembalian->translatedFormat('d M Y') }}</dd>
                    <dt>Lokasi rak</dt><dd>{{ $p->buku->lokasi ?: '-' }}</dd>
                </dl>

                @if ($p->label === 'terlambat')
                    <span class="badge terlambat">Terlambat {{ $p->hari_terlambat }} hari</span>
                    <span class="denda" style="margin-left:8px">Denda Rp {{ number_format($p->denda_tampil, 0, ',', '.') }}</span>
                @elseif ($p->sisa_hari === 0)
                    <span class="badge dipinjam">Batas kembali hari ini</span>
                @else
                    <span class="badge dipinjam">{{ $p->sisa_hari }} hari lagi</span>
                @endif
            </article>
        @endforeach
    </div>
@endif

<div class="card mt">
    <div class="card-head"><h2>Riwayat Peminjaman</h2><span>Buku yang sudah Anda kembalikan</span></div>

    @if ($riwayat->isEmpty())
        <p class="empty">Belum ada riwayat.</p>
    @else
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Buku</th><th>Dipinjam</th><th>Dikembalikan</th><th>Denda</th></tr></thead>
                <tbody>
                @foreach ($riwayat as $p)
                    <tr>
                        <td>{{ $p->buku->judul }}</td>
                        <td class="nowrap">{{ $p->tanggal_pinjam->translatedFormat('d M Y') }}</td>
                        <td class="nowrap">{{ $p->tanggal_dikembalikan?->translatedFormat('d M Y') ?? '-' }}</td>
                        <td>
                            @if ($p->denda > 0)<span class="denda">Rp {{ number_format($p->denda, 0, ',', '.') }}</span>@else - @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $riwayat->links() }}
    @endif
</div>
@endsection

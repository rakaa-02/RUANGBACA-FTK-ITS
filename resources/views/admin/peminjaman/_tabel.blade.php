{{-- Tabel peminjaman dipakai di halaman Peminjaman dan Buku Dipinjam. Variabel: $daftar (paginator) --}}
<div class="table-wrap">
    <table class="tbl">
        <thead>
        <tr>
            <th>No</th><th>Peminjam</th><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th>
            <th>Tgl Dikembalikan</th><th>Status</th><th>Denda</th><th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($daftar as $p)
            <tr>
                <td>{{ $daftar->firstItem() + $loop->index }}</td>
                <td>{{ $p->akun->Nickname }}<small>{{ $p->akun->NIM }}</small></td>
                <td>
                    {{ $p->buku->judul }}
                    <small>{{ $p->buku->no_induk }} · {{ $p->buku->lokasi }}</small>
                </td>
                <td class="nowrap">{{ $p->tanggal_pinjam->format('Y-m-d') }}</td>
                <td class="nowrap">{{ $p->batas_pengembalian->format('Y-m-d') }}</td>
                <td class="nowrap">{{ $p->tanggal_dikembalikan?->format('Y-m-d') ?? '-' }}</td>
                <td><span class="badge {{ $p->label }}">{{ ucfirst($p->label) }}</span></td>
                <td>
                    @if ($p->denda_tampil > 0)
                        <span class="denda">Rp {{ number_format($p->denda_tampil, 0, ',', '.') }}</span>
                        @if (! $p->sudah_kembali)<small>{{ $p->hari_terlambat }} hari</small>@endif
                    @else
                        -
                    @endif
                </td>
                <td>
                    <div class="aksi">
                        @unless ($p->sudah_kembali)
                            <form method="POST" action="{{ route('admin.peminjaman.kembalikan', $p) }}">
                                @csrf @method('PATCH')
                                <button class="btn sm green" type="submit">Kembalikan</button>
                            </form>
                        @endunless
                        <form method="POST" action="{{ route('admin.peminjaman.destroy', $p) }}" onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn sm danger" type="submit">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="empty">Tidak ada data peminjaman.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

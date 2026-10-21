@extends('layouts.app')

@section('title', 'Peminjaman')
@section('kicker', 'Sirkulasi')
@section('heading', 'Tambah Peminjaman Baru')
@section('lead', 'Masukkan detail peminjaman buku untuk anggota perpustakaan.')

@section('content')
<div class="card" style="max-width:820px">
    <form method="POST" action="{{ route('loans.store') }}" autocomplete="off">
        @csrf
        <div class="form-grid">
            <div class="field">
                <label for="member_id">Nickname Peminjam</label>
                <select class="input" id="member_id" name="member_id" required>
                    <option value="">— Pilih Nickname —</option>
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}" @selected(old('member_id') == $m->id)>{{ $m->nickname }}</option>
                    @endforeach
                </select>
                @error('member_id')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field ac">
                <label for="judul_cari">Judul Buku</label>
                <input class="input" id="judul_cari" type="text" placeholder="Ketik untuk mencari judul buku…" value="{{ old('judul_cari') }}">
                <input type="hidden" name="book_id" id="book_id" value="{{ old('book_id') }}">
                <div class="ac-list" id="ac-list" role="listbox"></div>
                <span class="ac-picked" id="ac-picked"></span>
                @error('book_id')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="tgl_pinjam">Tanggal Pinjam</label>
                <input class="input" type="date" id="tgl_pinjam" name="tgl_pinjam" value="{{ old('tgl_pinjam', $defaultPinjam) }}" required>
                @error('tgl_pinjam')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="batas_kembali">Batas Pengembalian</label>
                <input class="input" type="date" id="batas_kembali" name="batas_kembali" value="{{ old('batas_kembali', $defaultBatas) }}" required>
                @error('batas_kembali')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="status">Status Peminjaman</label>
                <select class="input" id="status" name="status" required>
                    <option value="dipinjam" @selected(old('status', 'dipinjam') === 'dipinjam')>Dipinjam</option>
                    <option value="dikembalikan" @selected(old('status') === 'dikembalikan')>Sudah dikembalikan</option>
                </select>
                @error('status')<span class="err">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-actions" style="justify-content:flex-end">
            <a href="{{ route('loans.index') }}" class="btn ghost">Batal</a>
            <button class="btn" type="submit">Simpan Data</button>
        </div>
    </form>
</div>

<div class="card mt">
    <div class="card-head">
        <h2>Daftar Peminjaman Terbaru</h2>
        <span>Denda Rp {{ number_format(\App\Models\Loan::DENDA_PER_HARI, 0, ',', '.') }} per hari keterlambatan</span>
    </div>

    <div class="table-wrap">
        <table class="ledger">
            <thead>
            <tr>
                <th>No</th><th>Peminjam</th><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th>
                <th>Tgl Dikembalikan</th><th>Status</th><th>Denda</th><th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($loans as $l)
                <tr>
                    <td class="num">{{ $loans->firstItem() + $loop->index }}</td>
                    <td class="title">{{ $l->member->nickname }}</td>
                    <td>{{ $l->book->judul }}</td>
                    <td class="mono">{{ $l->tgl_pinjam->format('Y-m-d') }}</td>
                    <td class="mono">{{ $l->batas_kembali->format('Y-m-d') }}</td>
                    <td class="mono">{{ $l->tgl_kembali?->format('Y-m-d') ?? '—' }}</td>
                    <td><span class="badge {{ $l->label_status }}">{{ ucfirst($l->label_status) }}</span></td>
                    <td>
                        @if ($l->denda > 0)
                            <span class="denda">Rp {{ number_format($l->denda, 0, ',', '.') }}</span>
                            <small style="display:block;color:var(--ink-soft)">{{ $l->hari_terlambat }} hari</small>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            @if ($l->status !== 'dikembalikan')
                                <form method="POST" action="{{ route('loans.return', $l) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn sm" type="submit">Kembalikan</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('loans.destroy', $l) }}" onsubmit="return confirm('Hapus catatan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn sm danger" type="submit">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty">Belum ada peminjaman.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $loans->links() }}
</div>
@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('judul_cari');
    const hidden = document.getElementById('book_id');
    const list = document.getElementById('ac-list');
    const picked = document.getElementById('ac-picked');
    const url = @json(route('books.search'));
    let timer, items = [], cursor = -1;

    function close() { list.classList.remove('open'); cursor = -1; }

    function choose(b) {
        input.value = b.judul;
        hidden.value = b.id;
        picked.textContent = '✓ ' + b.no_induk + ' · ' + b.lokasi;
        close();
    }

    function render() {
        list.innerHTML = '';
        if (!items.length) { close(); return; }
        items.forEach((b, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = i === cursor ? 'hi' : '';
            const t = document.createElement('span'); t.textContent = b.judul;
            const s = document.createElement('small'); s.textContent = b.no_induk + ' · ' + b.lokasi;
            btn.append(t, s);
            btn.addEventListener('mousedown', (e) => { e.preventDefault(); choose(b); });
            list.appendChild(btn);
        });
        list.classList.add('open');
    }

    input.addEventListener('input', () => {
        hidden.value = ''; picked.textContent = '';
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) { items = []; close(); return; }
        timer = setTimeout(async () => {
            try {
                const res = await fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
                items = await res.json();
                cursor = -1; render();
            } catch (e) { items = []; close(); }
        }, 220);
    });

    input.addEventListener('keydown', (e) => {
        if (!list.classList.contains('open')) return;
        if (e.key === 'ArrowDown') { cursor = (cursor + 1) % items.length; render(); e.preventDefault(); }
        else if (e.key === 'ArrowUp') { cursor = (cursor - 1 + items.length) % items.length; render(); e.preventDefault(); }
        else if (e.key === 'Enter' && cursor >= 0) { choose(items[cursor]); e.preventDefault(); }
        else if (e.key === 'Escape') close();
    });

    input.addEventListener('blur', () => setTimeout(close, 120));
})();
</script>
@endpush

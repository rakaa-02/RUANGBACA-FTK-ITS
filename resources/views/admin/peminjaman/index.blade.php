@extends('layouts.admin')

@section('title', 'Peminjaman')
@section('heading', 'Tambah Peminjaman Baru')
@section('lead', 'Masukkan detail peminjaman buku untuk anggota perpustakaan.')

@section('content')
<div class="card accent" style="max-width:760px;margin:0 auto">
    <form method="POST" action="{{ route('admin.peminjaman.store') }}" autocomplete="off">
        @csrf
        <div class="form-grid">
            <div class="field">
                <label for="id_akun">Nickname Peminjam</label>
                <select class="input" id="id_akun" name="id_akun" required>
                    <option value="">-- Pilih Nickname --</option>
                    @foreach ($akun as $a)
                        <option value="{{ $a->id }}" @selected(old('id_akun') == $a->id)>{{ $a->Nickname }}</option>
                    @endforeach
                </select>
                @error('id_akun')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field ac">
                <label for="judul_cari">Judul Buku</label>
                <input class="input" id="judul_cari" type="text" placeholder="Ketik untuk mencari judul buku...">
                <input type="hidden" name="id_buku" id="id_buku" value="{{ old('id_buku') }}">
                <div class="ac-list" id="ac-list" role="listbox"></div>
                <span class="ac-picked" id="ac-picked"></span>
                @error('id_buku')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="tanggal_pinjam">Tanggal Pinjam</label>
                <input class="input" type="date" id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', $tglPinjam) }}" required>
                @error('tanggal_pinjam')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="batas_pengembalian">Batas Pengembalian</label>
                <input class="input" type="date" id="batas_pengembalian" name="batas_pengembalian" value="{{ old('batas_pengembalian', $tglBatas) }}" required>
                @error('batas_pengembalian')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="status_peminjaman">Status Peminjaman</label>
                <select class="input" id="status_peminjaman" name="status_peminjaman" required>
                    <option value="tidak" @selected(old('status_peminjaman', 'tidak') === 'tidak')>Belum dikembalikan</option>
                    <option value="ya" @selected(old('status_peminjaman') === 'ya')>Sudah dikembalikan</option>
                </select>
                @error('status_peminjaman')<span class="err">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.peminjaman.index') }}" class="btn ghost">Batal</a>
            <button class="btn" type="submit">Simpan Data</button>
        </div>
    </form>
</div>

<div class="card accent mt">
    <div class="card-head">
        <h2>Daftar Peminjaman Terbaru</h2>
        <span>Denda Rp {{ number_format(\App\Models\Peminjaman::DENDA_PER_HARI, 0, ',', '.') }} per hari keterlambatan</span>
    </div>

    @include('admin.peminjaman._tabel', ['daftar' => $peminjaman])
    {{ $peminjaman->links() }}
</div>
@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('judul_cari');
    const hidden = document.getElementById('id_buku');
    const list = document.getElementById('ac-list');
    const picked = document.getElementById('ac-picked');
    const url = @json(route('admin.buku.cari'));
    let timer, items = [], cursor = -1;

    function tutup() { list.classList.remove('open'); cursor = -1; }

    function pilih(b) {
        if (b.dipinjam) return;               // eksemplar yang sedang keluar tidak bisa dipilih
        input.value = b.judul;
        hidden.value = b.id_buku;
        picked.textContent = '✓ ' + (b.no_induk || '-') + ' · ' + (b.lokasi || '-');
        tutup();
    }

    function gambar() {
        list.innerHTML = '';
        if (!items.length) { tutup(); return; }
        items.forEach((b, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = i === cursor ? 'hi' : '';
            const t = document.createElement('span'); t.textContent = b.judul;
            const s = document.createElement('small');
            s.textContent = [b.pengarang, b.no_induk, b.lokasi].filter(Boolean).join(' · ');
            btn.append(t, s);
            if (b.dipinjam) {
                const d = document.createElement('small'); d.className = 'dipakai'; d.textContent = 'Sedang dipinjam';
                btn.append(d);
            }
            btn.addEventListener('mousedown', (e) => { e.preventDefault(); pilih(b); });
            list.appendChild(btn);
        });
        list.classList.add('open');
    }

    input.addEventListener('input', () => {
        hidden.value = ''; picked.textContent = '';
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) { items = []; tutup(); return; }
        timer = setTimeout(async () => {
            try {
                const res = await fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
                items = await res.json(); cursor = -1; gambar();
            } catch (e) { items = []; tutup(); }
        }, 220);
    });

    input.addEventListener('keydown', (e) => {
        if (!list.classList.contains('open')) return;
        if (e.key === 'ArrowDown') { cursor = (cursor + 1) % items.length; gambar(); e.preventDefault(); }
        else if (e.key === 'ArrowUp') { cursor = (cursor - 1 + items.length) % items.length; gambar(); e.preventDefault(); }
        else if (e.key === 'Enter' && cursor >= 0) { pilih(items[cursor]); e.preventDefault(); }
        else if (e.key === 'Escape') tutup();
    });

    input.addEventListener('blur', () => setTimeout(tutup, 120));
})();
</script>
@endpush

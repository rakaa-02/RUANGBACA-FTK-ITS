@extends('layouts.admin')

@php $edit = $buku->exists; @endphp
@section('title', $edit ? 'Edit Buku' : 'Tambah Buku')
@section('heading', $edit ? 'Edit Buku' : 'Tambah Buku Baru')
@section('lead', 'Satu baris data = satu eksemplar buku.')

@section('content')
<div class="card accent" style="max-width:900px;margin:0 auto">
    <form method="POST" action="{{ $edit ? route('admin.buku.update', $buku) : route('admin.buku.store') }}">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="form-grid">
            <div class="field span2">
                <label for="judul">Judul Buku</label>
                <input class="input" id="judul" name="judul" placeholder="Contoh: Buku FTK ITS" value="{{ old('judul', $buku->judul) }}" required maxlength="255">
                @error('judul')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="pengarang">Pengarang</label>
                <input class="input" id="pengarang" name="pengarang" placeholder="Contoh: Budi Santoso" value="{{ old('pengarang', $buku->pengarang) }}" maxlength="255">
                @error('pengarang')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label for="penerbit">Penerbit</label>
                <input class="input" id="penerbit" name="penerbit" placeholder="Contoh: ITS Press" value="{{ old('penerbit', $buku->penerbit) }}" maxlength="255">
                @error('penerbit')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="kota_terbit">Kota Terbit</label>
                <input class="input" id="kota_terbit" name="kota_terbit" placeholder="Contoh: Surabaya" value="{{ old('kota_terbit', $buku->kota_terbit) }}" maxlength="255">
                @error('kota_terbit')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input class="input" id="tahun_terbit" type="number" name="tahun_terbit" placeholder="Contoh: 2023" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" required>
                <span class="hint">Isi 0 bila tahun terbit tidak diketahui.</span>
                @error('tahun_terbit')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="no_induk">No Induk</label>
                <input class="input" id="no_induk" name="no_induk" placeholder="Contoh: 001/FTK/2026" value="{{ old('no_induk', $buku->no_induk) }}" required maxlength="255">
                @error('no_induk')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label for="no_class">No Klasifikasi</label>
                <input class="input" id="no_class" name="no_class" placeholder="Contoh: 623.8 BUD s" value="{{ old('no_class', $buku->no_class) }}" maxlength="255">
                @error('no_class')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="isbn">ISBN</label>
                <input class="input" id="isbn" name="isbn" placeholder="Contoh: 978-602-1234-56-7" value="{{ old('isbn', $buku->isbn) }}" maxlength="255">
                @error('isbn')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label for="deskripsi_fisik">Deskripsi Fisik</label>
                <input class="input" id="deskripsi_fisik" name="deskripsi_fisik" placeholder="Contoh: xii, 250 hlm.; 21 cm" value="{{ old('deskripsi_fisik', $buku->deskripsi_fisik) }}" maxlength="255">
                @error('deskripsi_fisik')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="lokasi">Lokasi</label>
                <input class="input" id="lokasi" name="lokasi" placeholder="Contoh: RAK 25" value="{{ old('lokasi', $buku->lokasi) }}" required maxlength="255">
                @error('lokasi')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label for="tanggal">Tanggal Masuk</label>
                <input class="input" id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', optional($buku->tanggal)->format('Y-m-d')) }}" required>
                @error('tanggal')<span class="err">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.buku.index') }}" class="btn ghost">Batal</a>
            <button class="btn" type="submit">Simpan Data</button>
        </div>
    </form>
</div>
@endsection

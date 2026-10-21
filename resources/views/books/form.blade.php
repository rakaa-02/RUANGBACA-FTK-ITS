@extends('layouts.app')

@php $edit = $book->exists; @endphp
@section('title', $edit ? 'Edit Buku' : 'Tambah Buku')
@section('kicker', 'Koleksi')
@section('heading', $edit ? 'Edit Buku' : 'Tambah Buku Baru')
@section('lead', 'Pastikan nomor induk unik — ia dipakai untuk mengenali eksemplar.')

@section('content')
<div class="card" style="max-width:760px">
    <form method="POST" action="{{ $edit ? route('books.update', $book) : route('books.store') }}">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="form-grid">
            <div class="field" style="grid-column:1/-1">
                <label for="judul">Judul Buku</label>
                <input class="input" id="judul" name="judul" value="{{ old('judul', $book->judul) }}" required maxlength="255">
                @error('judul')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label for="no_induk">No Induk</label>
                <input class="input" id="no_induk" name="no_induk" value="{{ old('no_induk', $book->no_induk) }}" required maxlength="50">
                @error('no_induk')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label for="lokasi">Lokasi</label>
                <input class="input" id="lokasi" name="lokasi" value="{{ old('lokasi', $book->lokasi) }}" placeholder="mis. RAK 25" required maxlength="50">
                @error('lokasi')<span class="err">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('books.index') }}" class="btn ghost">Batal</a>
            <button class="btn" type="submit">Simpan Data</button>
        </div>
    </form>
</div>
@endsection

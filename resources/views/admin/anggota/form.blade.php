@extends('layouts.admin')

@php $edit = $akun->exists; @endphp
@section('title', $edit ? 'Edit Akun' : 'Tambah Akun')
@section('heading', $edit ? 'Edit Akun Mahasiswa' : 'Tambah Akun Mahasiswa')

@section('content')
<div class="card accent" style="max-width:720px;margin:0 auto">
    <form method="POST" action="{{ $edit ? route('admin.anggota.update', $akun) : route('admin.anggota.store') }}" autocomplete="off">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="form-grid">
            <div class="field">
                <label for="Nickname">Nickname (untuk login)</label>
                <input class="input" id="Nickname" name="Nickname" value="{{ old('Nickname', $akun->Nickname) }}" required maxlength="50">
                @error('Nickname')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="NIM">NIM</label>
                <input class="input" id="NIM" name="NIM" value="{{ old('NIM', $akun->NIM) }}" required maxlength="20">
                @error('NIM')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="Fakultas">Fakultas / Departemen</label>
                <input class="input" id="Fakultas" name="Fakultas" list="daftar-fakultas" value="{{ old('Fakultas', $akun->Fakultas) }}" required maxlength="100">
                <datalist id="daftar-fakultas">
                    @foreach ($fakultas as $f)<option value="{{ $f }}">@endforeach
                </datalist>
                @error('Fakultas')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="Password">Password {{ $edit ? '(kosongkan bila tidak diubah)' : '' }}</label>
                <input class="input" id="Password" type="text" name="Password" @required(! $edit) minlength="6" maxlength="100" autocomplete="new-password">
                @error('Password')<span class="err">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.anggota.index') }}" class="btn ghost">Batal</a>
            <button class="btn" type="submit">Simpan Data</button>
        </div>
    </form>
</div>
@endsection

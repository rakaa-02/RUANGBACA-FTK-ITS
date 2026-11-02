<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Ruang Baca FTK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login-bg">
    <form class="login-card" method="POST" action="{{ route('login.proses') }}">
        @csrf
        {{-- Ganti berkas public/img/logo.svg dengan logo asli (atau ubah src ini ke logo.png) --}}
        <img class="logo" src="{{ asset('img/logo.svg') }}" alt="Logo Ruang Baca FTK">

        <h1>Selamat Datang</h1>
        <p class="sub">Sistem Informasi Ruang Baca Fakultas Teknologi Kelautan</p>

        <div class="stack">
            <div class="field">
                <label for="peran">Masuk Sebagai</label>
                <select class="input" id="peran" name="peran">
                    <option value="visitor" @selected(old('peran', 'visitor') === 'visitor')>Visitor (Mahasiswa / Siswa)</option>
                    <option value="admin" @selected(old('peran') === 'admin')>Admin / Petugas</option>
                </select>
            </div>

            <div class="field">
                <label for="username">Nickname / Nama Pengguna</label>
                <input class="input" id="username" name="username" value="{{ old('username') }}" required autofocus autocomplete="username">
                @error('username')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input class="input" id="password" type="password" name="password" required autocomplete="current-password">
                @error('password')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="login-row">
                <label><input type="checkbox" id="tampil"> Tampilkan Password</label>
                <a href="{{ route('lupa') }}">Lupa Password?</a>
            </div>

            <button class="btn" type="submit">Masuk ke Portal</button>
        </div>

        <p class="login-foot">Belum punya akun? Hubungi petugas ruang baca.</p>
    </form>
</div>

<script>
    document.getElementById('tampil').addEventListener('change', function () {
        document.getElementById('password').type = this.checked ? 'text' : 'password';
    });
</script>
</body>
</html>

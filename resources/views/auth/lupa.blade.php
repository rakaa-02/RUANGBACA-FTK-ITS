<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password — Ruang Baca FTK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login-bg">
    <div class="login-card">
        <img class="logo" src="{{ asset('img/logo.svg') }}" alt="Logo Ruang Baca FTK">
        <h1>Lupa Password?</h1>
        <p class="sub">Akun dikelola oleh petugas ruang baca.</p>

        <div class="stack">
            <p style="margin:0">Temui petugas ruang baca dengan membawa kartu mahasiswa. Petugas akan mengatur ulang password Anda, lalu Anda bisa masuk kembali.</p>
            <a class="btn" href="{{ route('login') }}" style="text-decoration:none">Kembali ke halaman masuk</a>
        </div>
    </div>
</div>
</body>
</html>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') — Ruang Baca FTK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="vbar">
    <div class="vbar-in">
        <img src="{{ asset('img/logo.svg') }}" alt="Logo">
        <b>Ruang Baca FTK</b>
        <span class="sp"></span>
        <span class="nama">{{ session('nama') }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </div>
</header>

<div class="vwrap">
    @yield('content')
</div>
</body>
</html>

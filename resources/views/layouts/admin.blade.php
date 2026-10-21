<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Ruang Baca FTK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="app">
    <aside class="side">
        <a href="{{ route('admin.dashboard') }}" class="side-brand">
            <img src="{{ asset('img/logo.svg') }}" alt="Logo">
            <b>Ruang Baca<br>FTK</b>
        </a>

        <nav class="side-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">@include('partials.icons', ['nama' => 'dashboard']) Dashboard</a>
            <a href="{{ route('admin.buku.index') }}" class="{{ request()->routeIs('admin.buku.*') ? 'on' : '' }}">@include('partials.icons', ['nama' => 'buku']) Pendataan Buku</a>
            <a href="{{ route('admin.peminjaman.index') }}" class="{{ request()->routeIs('admin.peminjaman.*') ? 'on' : '' }}">@include('partials.icons', ['nama' => 'pinjam']) Peminjaman</a>
            <a href="{{ route('admin.dipinjam') }}" class="{{ request()->routeIs('admin.dipinjam') ? 'on' : '' }}">@include('partials.icons', ['nama' => 'jam']) Buku Dipinjam</a>
            <a href="{{ route('admin.anggota.index') }}" class="{{ request()->routeIs('admin.anggota.*') ? 'on' : '' }}">@include('partials.icons', ['nama' => 'orang']) Akun Mahasiswa</a>
            <a href="{{ route('admin.pengunjung.index') }}" class="{{ request()->routeIs('admin.pengunjung.*') ? 'on' : '' }}">@include('partials.icons', ['nama' => 'tumpuk']) Data Pengunjung</a>
        </nav>

        <div class="side-foot">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">@include('partials.icons', ['nama' => 'pintu']) Logout</button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div>
                <h1>@yield('heading')</h1>
                @hasSection('lead')<p class="sub">@yield('lead')</p>@endif
            </div>
            <div class="who">Halo, {{ session('nama') }}</div>
        </header>

        <div class="content">
            @if (session('sukses'))<div class="alert sukses">{{ session('sukses') }}</div>@endif
            @if (session('galat'))<div class="alert galat">{{ session('galat') }}</div>@endif

            @yield('content')
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>

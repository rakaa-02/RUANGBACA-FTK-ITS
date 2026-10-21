<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ruang Baca') — Ruang Baca FTK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,400;1,9..144,500&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="shell">
    <aside class="rail">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-seal">RB</span>
            <span><b>Ruang Baca</b><small>FTK</small></span>
        </a>

        <p class="nav-label">Menu</p>
        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}"><i>i.</i> Dashboard</a>
            <a href="{{ route('books.index') }}" class="{{ request()->routeIs('books.*') ? 'on' : '' }}"><i>ii.</i> Pendataan Buku</a>
            <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.*') ? 'on' : '' }}"><i>iii.</i> Peminjaman</a>
            <a href="{{ route('visits.index') }}" class="{{ request()->routeIs('visits.*') ? 'on' : '' }}"><i>iv.</i> Pengunjung</a>
        </nav>

        <div class="rail-foot">
            <a href="{{ route('checkin') }}" target="_blank" rel="noopener">Halaman check-in ↗</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </div>
    </aside>

    <main>
        <header class="masthead">
            <div>
                <div class="kicker">@yield('kicker')</div>
                <h1>@yield('heading')</h1>
                @hasSection('lead')<p>@yield('lead')</p>@endif
            </div>
            <div class="greet">Halo, {{ auth()->user()->name }} · {{ now()->translatedFormat('l, d F Y') }}</div>
        </header>

        @if (session('success'))<div class="alert success">{{ session('success') }}</div>@endif
        @if (session('info'))<div class="alert info">{{ session('info') }}</div>@endif

        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Hadir — Ruang Baca FTK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,400&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="libcard-wrap">
    <div class="libcard">
        <header>
            <div>
                <small>Ruang Baca FTK</small>
                <h1>Daftar Hadir</h1>
            </div>
            <span class="brand-seal" style="border-color:#efe6d2;color:#efe6d2">RB</span>
        </header>

        <form class="body" method="POST" action="{{ route('checkin.store') }}">
            @csrf

            @if (session('success'))<div class="alert success">{{ session('success') }}</div>@endif
            @if (session('info'))<div class="alert info">{{ session('info') }}</div>@endif

            <div class="stack">
                <div class="field">
                    <label for="nickname">Nama / Nickname</label>
                    <input class="input" id="nickname" name="nickname" value="{{ old('nickname') }}" required maxlength="100">
                    @error('nickname')<span class="err">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label for="nim">NIM</label>
                    <input class="input" id="nim" name="nim" value="{{ old('nim') }}" required maxlength="30" inputmode="numeric">
                    @error('nim')<span class="err">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label for="fakultas">Fakultas / Departemen</label>
                    <select class="input" id="fakultas" name="fakultas" required>
                        <option value="">— pilih —</option>
                        @foreach ($fakultas as $f)
                            <option value="{{ $f }}" @selected(old('fakultas') === $f)>{{ $f }}</option>
                        @endforeach
                    </select>
                    @error('fakultas')<span class="err">{{ $message }}</span>@enderror
                </div>

                <button class="btn" type="submit" style="justify-content:center">Catat Kehadiran</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard Analitik')

@section('content')
<div class="grid g3">
    <div class="card stat">
        <div><small>Peminjaman Bulan Ini</small><strong>{{ number_format($stats['peminjaman'], 0, ',', '.') }}</strong></div>
        <span class="ico navy">@include('partials.icons', ['nama' => 'pinjam'])</span>
    </div>
    <div class="card stat">
        <div><small>Buku Baru (Bulan Ini)</small><strong>{{ number_format($stats['buku_baru'], 0, ',', '.') }}</strong></div>
        <span class="ico green">@include('partials.icons', ['nama' => 'cek'])</span>
    </div>
    <div class="card stat">
        <div><small>Total Koleksi Buku</small><strong>{{ number_format($stats['koleksi'], 0, ',', '.') }}</strong></div>
        <span class="ico amber">@include('partials.icons', ['nama' => 'tumpuk'])</span>
    </div>

    {{-- Satu kartu "Buku Dipinjam": hari ini + kemarin; klik untuk melihat daftarnya --}}
    <a class="card stat" href="{{ route('admin.dipinjam', ['rentang' => 'hari-ini']) }}">
        <div>
            <small>Buku Dipinjam (Hari Ini)</small>
            <strong>{{ $stats['dipinjam_hari_ini'] }}</strong>
            <em>Kemarin: {{ $stats['dipinjam_kemarin'] }} buku</em>
        </div>
        <span class="ico">@include('partials.icons', ['nama' => 'buku'])</span>
    </a>
    <a class="card stat" href="{{ route('admin.dipinjam', ['rentang' => 'semua', 'status' => 'belum']) }}">
        <div><small>Belum Dikembalikan</small><strong>{{ $stats['belum_kembali'] }}</strong></div>
        <span class="ico amber">@include('partials.icons', ['nama' => 'pinjam'])</span>
    </a>
    <a class="card stat" href="{{ route('admin.dipinjam', ['rentang' => 'semua', 'status' => 'terlambat']) }}">
        <div><small>Terlambat</small><strong>{{ $stats['terlambat'] }}</strong></div>
        <span class="ico red">@include('partials.icons', ['nama' => 'awas'])</span>
    </a>
</div>

<form method="GET" class="toolbar mt" style="margin-bottom:0">
    <div class="left">
        <strong>Grafik tahun</strong>
        <select name="year" class="input" style="width:auto" onchange="this.form.submit()">
            @foreach ($years as $y)
                <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
            @endforeach
        </select>
    </div>
</form>

<div class="grid g-main mt">
    <section class="card">
        <div class="card-head">
            <h2>Statistik Peminjaman ({{ $year }})</h2>
            <span>Arahkan kursor ke batang untuk melihat jumlah peminjaman harian</span>
        </div>
        @if (count($chartHarian['data']))
            <div class="chart-box"><canvas id="chartHarian" role="img" aria-label="Grafik peminjaman harian"></canvas></div>
        @else
            <p class="empty">Belum ada peminjaman di tahun {{ $year }}.</p>
        @endif
    </section>

    <section class="card">
        <div class="card-head">
            <h2>Peminjaman by Fakultas</h2>
        </div>
        @if (count($chartFakultas['data']))
            <div class="chart-box"><canvas id="chartFakultas" role="img" aria-label="Peminjaman per fakultas"></canvas></div>
        @else
            <p class="empty">Belum ada data.</p>
        @endif
    </section>
</div>

<section class="card mt">
    <div class="card-head">
        <h2>Buku Baru per Bulan</h2>
        <span>total {{ number_format($stats['koleksi'], 0, ',', '.') }} buku dalam koleksi</span>
    </div>
    <div class="chart-box short"><canvas id="chartBuku" role="img" aria-label="Buku baru per bulan"></canvas></div>
</section>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const BIRU = '#60a5fa', BIRU_HOVER = '#1e3a8a', TEKS = '#64748b', GRID = 'rgba(100,116,139,.16)';
    const harian = @json($chartHarian);
    const fak    = @json($chartFakultas);
    const buku   = @json($chartBuku);

    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.color = TEKS;

    const tooltip = {
        backgroundColor: '#0f1b3d',
        titleFont: { weight: '700', size: 13 },
        bodyFont: { size: 13 },
        padding: 10,
        cornerRadius: 6,
        displayColors: false,
    };
    const sumbuY = { grid: { color: GRID }, border: { display: false }, ticks: { precision: 0 }, beginAtZero: true };

    // 1) Peminjaman harian — tooltip muncul saat kursor berada di kolom mana pun
    const elH = document.getElementById('chartHarian');
    if (elH) {
        new Chart(elH, {
            type: 'bar',
            data: {
                labels: harian.labels,
                datasets: [{ label: 'Peminjaman Harian', data: harian.data, backgroundColor: BIRU, hoverBackgroundColor: BIRU_HOVER, borderRadius: 2 }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltip, callbacks: { title: (i) => i[0].label, label: (c) => 'Peminjaman: ' + c.parsed.y + ' buku' } },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 55, autoSkip: true, maxTicksLimit: 16, font: { size: 11 } } },
                    y: sumbuY,
                },
            },
        });
    }

    // 2) Rata-rata per fakultas
    const elF = document.getElementById('chartFakultas');
    if (elF) {
        new Chart(elF, {
            type: 'bar',
            data: { labels: fak.labels, datasets: [{ data: fak.data, backgroundColor: BIRU, hoverBackgroundColor: BIRU_HOVER, borderRadius: 3 }] },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'nearest', axis: 'y', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltip, callbacks: { label: (c) => c.parsed.x + ' peminjaman' } },
                },
                scales: { x: sumbuY, y: { grid: { display: false }, ticks: { font: { size: 11 } } } },
            },
        });
    }

    // 3) Buku baru per bulan
    new Chart(document.getElementById('chartBuku'), {
        type: 'bar',
        data: { labels: buku.labels, datasets: [{ label: 'Buku Baru', data: buku.data, backgroundColor: BIRU, hoverBackgroundColor: BIRU_HOVER, borderRadius: 3 }] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: { ...tooltip, callbacks: { label: (c) => c.parsed.y + ' buku baru' } } },
            scales: { x: { grid: { display: false } }, y: sumbuY },
        },
    });
})();
</script>
@endpush

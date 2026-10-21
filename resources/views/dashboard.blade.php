@extends('layouts.app')

@section('title', 'Dashboard')
@section('kicker', 'Ringkasan')
@section('heading', 'Dashboard Analitik')
@section('lead', 'Pantau kunjungan, peminjaman, dan koleksi ruang baca dalam satu halaman.')

@section('content')
<div class="grid g4">
    <div class="card stat">
        <label>Pengunjung bulan ini</label>
        <strong>{{ number_format($stats['pengunjung'], 0, ',', '.') }}</strong>
        <em>{{ now()->translatedFormat('F Y') }}</em>
    </div>
    <div class="card stat">
        <label>Peminjaman bulan ini</label>
        <strong>{{ number_format($stats['peminjaman'], 0, ',', '.') }}</strong>
        <em>transaksi tercatat</em>
    </div>
    <div class="card stat">
        <label>Buku baru (bulan ini)</label>
        <strong>{{ number_format($stats['buku_baru'], 0, ',', '.') }}</strong>
        <em>judul ditambahkan</em>
    </div>
    <div class="card stat">
        <label>Total koleksi buku</label>
        <strong>{{ number_format($stats['koleksi'], 0, ',', '.') }}</strong>
        <em>eksemplar di rak</em>
    </div>
</div>

<form method="GET" class="toolbar mt" style="margin-bottom:0">
    <span class="kicker" style="margin:0">Grafik tahun</span>
    <select name="year" class="input" style="width:auto" onchange="this.form.submit()">
        @foreach ($years as $y)
            <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
        @endforeach
    </select>
</form>

<div class="grid g-main mt">
    <section class="card">
        <div class="card-head">
            <h2>Statistik Pengunjung ({{ $year }})</h2>
            <span>Arahkan kursor ke batang untuk melihat jumlah harian</span>
        </div>
        @if (count($chartHarian['data']))
            <div class="chart-box"><canvas id="chartHarian" aria-label="Grafik kunjungan harian" role="img"></canvas></div>
        @else
            <p class="empty">Belum ada kunjungan di tahun {{ $year }}.</p>
        @endif
    </section>

    <section class="card">
        <div class="card-head">
            <h2>Rata-rata Pengunjung by Fakultas</h2>
        </div>
        <span style="font-size:13px;color:var(--ink-soft)">Rata-rata per hari yang ada kunjungan</span>
        @if (count($chartFakultas['data']))
            <div class="chart-box" style="margin-top:10px"><canvas id="chartFakultas" aria-label="Rata-rata pengunjung per fakultas" role="img"></canvas></div>
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
    <div class="chart-box short"><canvas id="chartBuku" aria-label="Buku baru per bulan" role="img"></canvas></div>
</section>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const GREEN = '#2f5d46', COPPER = '#b2602b', INK = '#1c2b24', SOFT = '#55625a', GRID = 'rgba(28,43,36,.09)';
    const harian  = @json($chartHarian);
    const fak     = @json($chartFakultas);
    const buku    = @json($chartBuku);

    Chart.defaults.font.family = "'Public Sans', system-ui, sans-serif";
    Chart.defaults.color = SOFT;

    // Tooltip bergaya kertas: tanggal sebagai judul, jumlah di bawahnya
    const tooltip = {
        backgroundColor: INK,
        titleFont: { family: "'Fraunces', serif", size: 14, weight: '600' },
        bodyFont: { size: 13 },
        padding: 12,
        cornerRadius: 4,
        displayColors: false,
        caretSize: 6,
    };

    const axis = {
        grid: { color: GRID },
        border: { display: false },
        ticks: { precision: 0 },
    };

    // 1) Kunjungan harian — tooltip muncul saat kursor di atas kolom mana pun
    const elH = document.getElementById('chartHarian');
    if (elH) {
        new Chart(elH, {
            type: 'bar',
            data: {
                labels: harian.labels,
                datasets: [{
                    label: 'Kunjungan harian',
                    data: harian.data,
                    backgroundColor: GREEN,
                    hoverBackgroundColor: COPPER,
                    borderRadius: 2,
                    barPercentage: .85,
                    categoryPercentage: .9,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltip,
                        callbacks: {
                            title: (items) => items[0].label,
                            label: (ctx) => ctx.parsed.y + ' pengunjung',
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 60, autoSkip: true, maxTicksLimit: 14, font: { size: 11 } } },
                    y: { ...axis, beginAtZero: true },
                },
            },
        });
    }

    // 2) Rata-rata per fakultas
    const elF = document.getElementById('chartFakultas');
    if (elF) {
        new Chart(elF, {
            type: 'bar',
            data: {
                labels: fak.labels,
                datasets: [{ data: fak.data, backgroundColor: COPPER, hoverBackgroundColor: GREEN, borderRadius: 2, barPercentage: .7 }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'nearest', axis: 'y', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltip,
                        callbacks: {
                            label: (ctx) => 'Rata-rata ' + ctx.parsed.x.toString().replace('.', ',') + ' / hari',
                            afterLabel: (ctx) => 'Total ' + fak.total[ctx.dataIndex] + ' kunjungan',
                        },
                    },
                },
                scales: {
                    x: { ...axis, beginAtZero: true },
                    y: { grid: { display: false }, ticks: { font: { size: 12 } } },
                },
            },
        });
    }

    // 3) Buku baru per bulan
    new Chart(document.getElementById('chartBuku'), {
        type: 'bar',
        data: {
            labels: buku.labels,
            datasets: [{ data: buku.data, backgroundColor: GREEN, hoverBackgroundColor: COPPER, borderRadius: 2, barPercentage: .6 }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: { ...tooltip, callbacks: { label: (ctx) => ctx.parsed.y + ' buku baru' } },
            },
            scales: { x: { grid: { display: false } }, y: { ...axis, beginAtZero: true } },
        },
    });
})();
</script>
@endpush

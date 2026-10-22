<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Visitor;
use App\Http\Middleware\PeranMiddleware;
use Illuminate\Support\Facades\Route;

// Pintu masuk: arahkan sesuai peran (atau ke login)
Route::get('/', function () {
    return match (session('peran')) {
        'admin' => redirect()->route('admin.dashboard'),
        'visitor' => redirect()->route('visitor.beranda'),
        default => redirect()->route('login'),
    };
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.proses');
Route::get('/lupa-password', [AuthController::class, 'lupaPassword'])->name('lupa');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Admin ─────────────────────────────────────────────────────────────
Route::middleware(PeranMiddleware::class.':admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/buku/export', [Admin\BukuController::class, 'export'])->name('buku.export');
    Route::get('/buku/cari', [Admin\BukuController::class, 'cari'])->name('buku.cari');
    Route::resource('buku', Admin\BukuController::class)->except('show')->parameters(['buku' => 'buku']);

    Route::get('/peminjaman', [Admin\PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman', [Admin\PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::patch('/peminjaman/{peminjaman}/kembalikan', [Admin\PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    Route::delete('/peminjaman/{peminjaman}', [Admin\PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');

    Route::get('/dipinjam', [Admin\DipinjamController::class, 'index'])->name('dipinjam');

    Route::resource('anggota', Admin\AnggotaController::class)->except('show')->parameters(['anggota' => 'akun']);

    Route::get('/peminjam', [Admin\PeminjamController::class, 'index'])->name('peminjam.index');
    Route::get('/peminjam/{akun}', [Admin\PeminjamController::class, 'show'])->name('peminjam.show');
});

// ── Mahasiswa / siswa ────────────────────────────────────────────────
Route::middleware(PeranMiddleware::class.':visitor')->prefix('mahasiswa')->name('visitor.')->group(function () {
    Route::get('/beranda', [Visitor\BerandaController::class, 'index'])->name('beranda');
});

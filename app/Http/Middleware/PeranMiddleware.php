<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi halaman berdasarkan peran di sesi: "admin" atau "visitor".
 * Dipakai di routes/web.php sebagai PeranMiddleware::class.':admin'
 */
class PeranMiddleware
{
    public function handle(Request $request, Closure $next, string $peran): Response
    {
        $sekarang = $request->session()->get('peran');

        if ($sekarang === $peran) {
            return $next($request);
        }

        // sudah login tapi salah halaman → kembalikan ke beranda perannya
        if ($sekarang === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($sekarang === 'visitor') {
            return redirect()->route('visitor.beranda');
        }

        return redirect()->route('login');
    }
}

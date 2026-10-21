<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Akun;
use App\Models\Kunjungan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($peran = $request->session()->get('peran')) {
            return redirect()->route($peran === 'admin' ? 'admin.dashboard' : 'visitor.beranda');
        }

        return view('auth.login');
    }

    public function lupaPassword(): View
    {
        return view('auth.lupa');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'peran' => ['required', 'in:admin,visitor'],
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $gagal = back()
            ->withErrors(['username' => 'Nama pengguna atau password salah.'])
            ->onlyInput('username', 'peran');

        if ($data['peran'] === 'admin') {
            $admin = Admin::where('username', $data['username'])->first();
            if (! $admin || ! $this->cekPassword($data['password'], $admin->password, fn ($h) => $admin->forceFill(['password' => $h])->save())) {
                return $gagal;
            }

            $request->session()->regenerate();
            $request->session()->put(['peran' => 'admin', 'uid' => $admin->id_admin, 'nama' => $admin->username]);

            return redirect()->intended(route('admin.dashboard'));
        }

        $akun = Akun::where('Nickname', $data['username'])->first();
        if (! $akun || ! $this->cekPassword($data['password'], $akun->Password, fn ($h) => $akun->forceFill(['Password' => $h])->save())) {
            return $gagal;
        }

        $request->session()->regenerate();
        $request->session()->put(['peran' => 'visitor', 'uid' => $akun->id, 'nama' => $akun->Nickname]);

        // login mahasiswa = tercatat hadir hari ini (sekali per hari)
        Kunjungan::firstOrCreate(
            ['id_akun' => $akun->id, 'tanggal' => now()->toDateString()],
            ['fakultas' => $akun->Fakultas, 'jam' => now()->format('H:i:s')]
        );

        return redirect()->route('visitor.beranda');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Mendukung password ber-hash (bcrypt) maupun teks biasa dari database lama.
     * Password teks biasa otomatis diganti hash begitu pemiliknya berhasil login.
     */
    private function cekPassword(string $input, string $tersimpan, callable $simpanHash): bool
    {
        $sudahHash = password_get_info($tersimpan)['algo'] !== null;

        if ($sudahHash) {
            return password_verify($input, $tersimpan);
        }

        if (! hash_equals($tersimpan, $input)) {
            return false;
        }

        $simpanHash(Hash::make($input));

        return true;
    }
}

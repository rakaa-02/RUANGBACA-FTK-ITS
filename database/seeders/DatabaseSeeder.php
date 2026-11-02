<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Aman dijalankan di database yang sudah berisi data lama:
 * hanya membuat admin awal bila tabel `admin` masih kosong.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('admin')->count() === 0) {
            DB::table('admin')->insert([
                'username' => 'admin',
                'password' => Hash::make('ganti-password-ini'),
                'nama_lengkap' => 'Petugas Ruang Baca FTK',
                'email' => null,
            ]);

            $this->command?->warn("Admin awal dibuat: username 'admin', password 'ganti-password-ini'. Segera ganti.");
        }
    }
}

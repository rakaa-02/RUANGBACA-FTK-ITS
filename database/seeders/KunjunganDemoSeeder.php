<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * OPSIONAL — hanya untuk mencoba tampilan grafik.
 * Mengisi tabel `kunjungan` dengan data acak dari akun yang sudah ada.
 * Jalankan:  php artisan db:seed --class=KunjunganDemoSeeder
 * Hapus lagi: DELETE FROM kunjungan;   (di HeidiSQL)
 */
class KunjunganDemoSeeder extends Seeder
{
    public function run(): void
    {
        $akun = DB::table('akun')->get(['id', 'Fakultas']);
        if ($akun->isEmpty()) {
            $this->command?->warn('Tidak ada akun. Buat akun mahasiswa dulu.');

            return;
        }

        $now = now();
        $baris = [];

        for ($d = Carbon::create($now->year, 1, 1); $d->lte($now); $d->addDay()) {
            if ($d->isWeekend()) {
                continue;
            }

            $jumlah = rand(0, 100) < 6 ? rand(10, 15) : rand(1, 5);

            foreach ($akun->shuffle()->take(min($jumlah, $akun->count())) as $a) {
                $baris[] = [
                    'id_akun' => $a->id,
                    'fakultas' => $a->Fakultas,
                    'tanggal' => $d->toDateString(),
                    'jam' => sprintf('%02d:%02d:00', rand(8, 16), rand(0, 59)),
                    'created_at' => $d->toDateTimeString(),
                ];
            }
        }

        foreach (array_chunk($baris, 300) as $potongan) {
            DB::table('kunjungan')->insertOrIgnore($potongan);
        }

        $this->command?->info(count($baris).' data kunjungan contoh dibuat.');
    }
}

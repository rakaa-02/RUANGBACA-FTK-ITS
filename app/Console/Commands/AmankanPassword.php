<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AmankanPassword extends Command
{
    protected $signature = 'rb:amankan-password';

    protected $description = 'Ubah semua password teks biasa (admin & akun) di database menjadi hash bcrypt';

    public function handle(): int
    {
        $jumlah = 0;

        foreach ([['admin', 'id_admin', 'password'], ['akun', 'id', 'Password']] as [$tabel, $pk, $kolom]) {
            foreach (DB::table($tabel)->get([$pk, $kolom]) as $baris) {
                $nilai = $baris->$kolom;

                if (password_get_info($nilai)['algo'] === null) {
                    DB::table($tabel)->where($pk, $baris->$pk)->update([$kolom => Hash::make($nilai)]);
                    $jumlah++;
                }
            }
        }

        $this->info("Selesai. {$jumlah} password diubah menjadi hash.");

        return self::SUCCESS;
    }
}

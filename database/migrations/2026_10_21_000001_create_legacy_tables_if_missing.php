<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skema ini meniru database SIM lama (rb_db).
 * Bila Anda sudah mengimpor file .sql lama, tabel-tabel ini sudah ada dan akan DILEWATI,
 * sehingga data lama tidak tersentuh. Bila belum, tabel dibuat kosong.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin')) {
            Schema::create('admin', function (Blueprint $t) {
                $t->integer('id_admin', true);
                $t->string('username', 50)->unique();
                $t->string('password', 255);
                $t->string('nama_lengkap', 100);
                $t->string('email', 100)->nullable();
                $t->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('akun')) {
            Schema::create('akun', function (Blueprint $t) {
                $t->integer('id', true);
                $t->string('Nickname', 50)->unique();
                $t->string('Password', 255);
                $t->string('Fakultas', 100);
                $t->string('NIM', 20)->unique();
                $t->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('buku')) {
            Schema::create('buku', function (Blueprint $t) {
                $t->integer('id_buku', true);
                $t->date('tanggal')->nullable();
                $t->string('judul', 255)->nullable();
                $t->string('pengarang', 255)->nullable();
                $t->string('kota_terbit', 255)->nullable();
                $t->string('penerbit', 255)->nullable();
                $t->year('tahun_terbit');
                $t->string('no_class', 255)->nullable();
                $t->string('no_induk', 255)->nullable();
                $t->string('deskripsi_fisik', 255)->nullable();
                $t->string('isbn', 255)->nullable();
                $t->string('lokasi', 255)->nullable();
            });
        }

        if (! Schema::hasTable('peminjaman')) {
            Schema::create('peminjaman', function (Blueprint $t) {
                $t->integer('id_peminjaman', true);
                $t->integer('id_akun');
                $t->integer('id_buku');
                $t->date('tanggal_pinjam');
                $t->date('batas_pengembalian');
                $t->date('tanggal_dikembalikan')->nullable();
                $t->enum('status_peminjaman', ['ya', 'tidak', 'terlambat'])->default('tidak');
                $t->integer('denda')->default(0);
                $t->timestamp('created_at')->useCurrent();

                $t->foreign('id_akun')->references('id')->on('akun')->cascadeOnDelete()->cascadeOnUpdate();
                $t->foreign('id_buku')->references('id_buku')->on('buku')->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        // Sengaja kosong: tabel lama berisi data asli, jangan dihapus oleh rollback.
    }
};

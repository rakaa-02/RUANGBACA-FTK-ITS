<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kunjungan')) {
            return;
        }

        Schema::create('kunjungan', function (Blueprint $t) {
            $t->integer('id_kunjungan', true);
            $t->integer('id_akun');          // sama tipe dengan akun.id (int signed)
            $t->string('fakultas', 100);     // salinan saat login, agar statistik tidak berubah bila akun diedit
            $t->date('tanggal')->index();
            $t->time('jam');
            $t->timestamp('created_at')->useCurrent();

            $t->unique(['id_akun', 'tanggal']); // satu akun dihitung sekali per hari
            $t->foreign('id_akun')->references('id')->on('akun')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};

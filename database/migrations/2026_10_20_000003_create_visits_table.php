<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('fakultas', 80);
            $table->date('tanggal')->index();
            $table->time('jam');
            $table->timestamps();

            // satu anggota hanya tercatat satu kali per hari
            $table->unique(['member_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->date('tgl_pinjam');
            $table->date('batas_kembali');
            $table->date('tgl_kembali')->nullable();
            $table->string('status', 20)->default('dipinjam'); // dipinjam | dikembalikan
            $table->timestamps();

            $table->index('tgl_pinjam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

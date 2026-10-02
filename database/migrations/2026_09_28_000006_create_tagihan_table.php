<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('siswa_id', 20);
            $table->foreign('siswa_id')->references('nis')->on('siswa')
            ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("jenis_pembayaran_id")->constrained('jenis_pembayaran')->cascadeOnDelete();
            $table->foreignId("tahun_ajaran_id")->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->string("periode");
            $table->unsignedBigInteger("jumlah");
            $table->date("jatuh_tempo");
            $table->enum("status", ["belum", "cicil", "lunas"])->default("belum");
            $table->timestamps();

            $table->unique(["siswa_id", "jenis_pembayaran_id", "tahun_ajaran_id", "periode"], "tagihan_unik");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};

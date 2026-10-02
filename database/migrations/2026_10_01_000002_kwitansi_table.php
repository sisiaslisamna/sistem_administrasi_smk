<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kwitansi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->string('siswa_id', 20);
            $table->foreign('siswa_id')->references('nis')->on('siswa')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran');
            $table->foreignId('user_id')->constrained();
            $table->date('tanggal');
            $table->unsignedBigInteger('total');
            $table->string('metode')->default('tunai');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreignId('kwitansi_id')->nullable()->after('tagihan_id')
                ->constrained('kwitansi')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kwitansi_id');
        });
        Schema::dropIfExists('kwitansi');
    }
};
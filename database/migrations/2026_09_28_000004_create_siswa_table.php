<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->string('nis', 20)->primary();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun_masuk');
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 15);
            $table->string('email')->nullable();
            $table->string('nama_wali');
            $table->string('no_telepon_wali', 15);
            $table->text('alamat_wali');
            $table->enum('status', ['aktif', 'lulus', 'pindah'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};

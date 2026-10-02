<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tagihan_id")->constrained('tagihan')->cascadeOnDelete();
            $table->foreignId("user_id")->constrained();
            $table->date("tanggal_bayar");
            $table->unsignedBigInteger("jumlah_bayar");
            $table->string("metode")->default("tunai");
            $table->string("keterangan")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};

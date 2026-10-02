<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurusan', function (Blueprint $table) {
            $table->unsignedBigInteger('spp')->default(0)->after('nama');
        });

        foreach (['DKV' => 220000, 'BC' => 220000, 'TB' => 250000, 'MP' => 200000] as $kode => $spp) {
            DB::table('jurusan')->where('kode', $kode)->update(['spp' => $spp]);
        }
    }

    public function down(): void
    {
        Schema::table('jurusan', function (Blueprint $table) {
            $table->dropColumn('spp');
        });
    }
};
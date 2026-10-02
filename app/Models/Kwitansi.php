<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kwitansi extends Model
{
    protected $table = 'kwitansi';
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date'];

    public function siswa()       { return $this->belongsTo(Siswa::class, 'siswa_id', 'nis'); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function user()        { return $this->belongsTo(User::class); }
    public function pembayaran()  { return $this->hasMany(Pembayaran::class); }
}
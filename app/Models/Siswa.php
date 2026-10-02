<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $guarded = [];
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'siswa_id', 'nis');
    }
}

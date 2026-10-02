<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
    protected $guarded = [];
    protected $casts = ['aktif' => 'boolean'];

    public static function aktif(): ?self
    {
        return static::where('aktif', true)->first();
    }
}
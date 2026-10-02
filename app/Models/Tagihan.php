<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $guarded = [];
    protected $casts = ['jatuh_tempo' => 'date'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'nis');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisPembayaran::class, 'jenis_pembayaran_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function getTotalDibayarAttribute(): int
    {
        return (int) $this->pembayaran->sum('jumlah_bayar');
    }

    public function getSisaAttribute(): int
    {
        return max(0, $this->jumlah - $this->total_dibayar);
    }

    /** Hitung ulang status (belum / cicil / lunas) setelah ada pembayaran. */
    public function refreshStatus(): void
    {
        $dibayar = $this->pembayaran()->sum('jumlah_bayar');
        $this->status = $dibayar >= $this->jumlah ? 'lunas' : ($dibayar > 0 ? 'cicil' : 'belum');
        $this->save();
    }

    /** Tunggakan = belum lunas DAN sudah melewati jatuh tempo. */
    public function scopeTunggakan($query)
    {
        return $query->where('status', '!=', 'lunas')
                     ->whereDate('jatuh_tempo', '<', now());
    }
}

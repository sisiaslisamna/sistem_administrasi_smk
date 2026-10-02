<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;

class DashboardController extends Controller
{
    public function index()
    {
        $ta = TahunAjaran::aktif();

        $sppTagihan = Tagihan::whereHas('jenis', fn($q) => $q->where('nama', 'SPP'))
        ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id));
        
        $sppTerbayar = Pembayaran::whereIn('tagihan_id', (clone $sppTagihan)->select('id'))->sum('jumlah_bayar');
        $sppLunas    = (clone $sppTagihan)->where('status', 'lunas')->count();

        return view('dashboard', [
            'ta'          => $ta,
            'totalSiswa'  => Siswa::where('status', 'aktif')->count(),
            'sppTerbayar' => $sppTerbayar,
            'sppLunas'    => $sppLunas,
            'terbaru'     => Kwitansi::with('siswa')->latest('id')->limit(8)->get(),
        ]);
    }
}
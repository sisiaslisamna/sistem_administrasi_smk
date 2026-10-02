<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use App\Models\Jurusan;
use App\Models\Kwitansi;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SppController extends Controller
{
    public const BULAN = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    /** Jenis "SPP" dibuat otomatis jika belum ada (halaman jenis pembayaran sudah dihapus). */
    private function jenisSpp(): JenisPembayaran
    {
        return JenisPembayaran::firstOrCreate(
            ['nama' => 'SPP'],
            ['tipe' => 'bulanan', 'nominal' => 0]
        );
    }

    /** Tahun ajaran aktif; jika belum ada, dibuat otomatis (Juli-Juni) dari tanggal hari ini. */
    private function tahunAktif(): TahunAjaran
    {
        return TahunAjaran::aktif() ?? TahunAjaran::create([
            'nama'  => (now()->month >= 7 ? now()->year : now()->year - 1)
                     . '/' . (now()->month >= 7 ? now()->year + 1 : now()->year),
            'aktif' => true,
        ]);
    }

    /** Jatuh tempo tgl 10 tiap bulan; Juli-Des ikut tahun awal, Jan-Jun tahun akhir. */
    private function jatuhTempo(TahunAjaran $ta, string $bulan): Carbon
    {
        $no   = array_search($bulan, self::BULAN) + 1;
        $awal = (int) substr($ta->nama, 0, 4);

        return Carbon::create($no >= 7 ? $awal : $awal + 1, $no, 10);
    }

    // ===== Data SPP (admin + kepala sekolah) =====
    public function index(Request $request)
    {
        $jenis = $this->jenisSpp();
        $ta    = $this->tahunAktif();

        $siswa = Siswa::with([
                'kelas.jurusan',
                'tagihan' => fn($q) => $q->where('jenis_pembayaran_id', $jenis->id)
                    ->where('tahun_ajaran_id', $ta->id),
            ])
            ->where('status', 'aktif')
            ->when($request->q, fn($q, $v) => $q->where(fn($w) =>
                $w->where('nama', 'like', "%$v%")->orWhere('nis', 'like', "%$v%")))
            ->when($request->tingkat, fn($q, $v) => $q->whereHas('kelas', fn($k) => $k->where('tingkat', $v)))
            ->when($request->jurusan_id, fn($q, $v) => $q->whereHas('kelas', fn($k) => $k->where('jurusan_id', $v)))
            ->orderBy('nis')
            ->paginate(20)
            ->withQueryString();

        return view('halamanspp.index', [
            'siswa'   => $siswa,
            'ta'      => $ta,
            'bulan'   => self::BULAN,
            'jurusan' => Jurusan::orderBy('nama')->get(),
        ]);
    }

    // ===== Kwitansi (admin) =====
    public function createKwitansi(Request $request)
    {
        $ta    = $this->tahunAktif();
        $siswa = $request->nis ? Siswa::with('kelas.jurusan')->findOrFail($request->nis) : null;

        $bulanLunas = [];
        if ($siswa) {
            $bulanLunas = Tagihan::where('siswa_id', $siswa->nis)
                ->where('jenis_pembayaran_id', $this->jenisSpp()->id)
                ->where('tahun_ajaran_id', $ta->id)
                ->where('status', 'lunas')
                ->pluck('periode')->all();
        }

        return view('halamanspp.create', [
            'ta'          => $ta,
            'siswa'       => $siswa,
            'bulan'       => self::BULAN,
            'bulanLunas'  => $bulanLunas,
            'daftarSiswa' => Siswa::where('status', 'aktif')->orderBy('nis')->get(['nis', 'nama']),
        ]);
    }

    public function storeKwitansi(Request $request)
    {
        $data = $request->validate([
            'siswa_id'   => 'required|exists:siswa,nis',
            'bulan'      => 'required|array|min:1',
            'bulan.*'    => 'in:' . implode(',', self::BULAN),
            'tanggal'    => 'required|date',
            'metode'     => 'required|in:tunai,transfer',
            'keterangan' => 'nullable|max:255',
        ]);

        $ta      = $this->tahunAktif();
        $jenis   = $this->jenisSpp();
        $siswa   = Siswa::with('kelas.jurusan')->findOrFail($data['siswa_id']);
        $nominal = $siswa->kelas->jurusan->spp;

        if ($nominal <= 0) {
            return back()->withErrors(['siswa_id' => 'Nominal SPP jurusan ini belum diatur.'])->withInput();
        }

        $kwitansi = DB::transaction(function () use ($data, $ta, $jenis, $siswa, $nominal, $request) {
            $kwitansi = Kwitansi::create([
                'nomor'           => 'tmp-' . uniqid(),
                'siswa_id'        => $siswa->nis,
                'tahun_ajaran_id' => $ta->id,
                'user_id'         => $request->user()->id,
                'tanggal'         => $data['tanggal'],
                'total'           => 0,
                'metode'          => $data['metode'],
                'keterangan'      => $data['keterangan'] ?? null,
            ]);

            $total = 0;

            foreach ($data['bulan'] as $bulan) {
                $tagihan = Tagihan::firstOrCreate(
                    [
                        'siswa_id'            => $siswa->nis,
                        'jenis_pembayaran_id' => $jenis->id,
                        'tahun_ajaran_id'     => $ta->id,
                        'periode'             => $bulan,
                    ],
                    [
                        'jumlah'      => $nominal,
                        'jatuh_tempo' => $this->jatuhTempo($ta, $bulan),
                        'status'      => 'belum',
                    ]
                );

                $sisa = $tagihan->sisa;
                if ($sisa <= 0) {
                    continue; // bulan ini sudah lunas
                }

                Pembayaran::create([
                    'tagihan_id'    => $tagihan->id,
                    'kwitansi_id'   => $kwitansi->id,
                    'user_id'       => $request->user()->id,
                    'tanggal_bayar' => $data['tanggal'],
                    'jumlah_bayar'  => $sisa,
                    'metode'        => $data['metode'],
                    'keterangan'    => "SPP $bulan",
                ]);

                $tagihan->refreshStatus();
                $total += $sisa;
            }

            if ($total === 0) {
                throw ValidationException::withMessages(['bulan' => 'Bulan yang dipilih sudah lunas semua.']);
            }

            $kwitansi->update([
                'nomor' => 'KW/' . $ta->nama . '/' . str_pad($kwitansi->id, 5, '0', STR_PAD_LEFT),
                'total' => $total,
            ]);

            return $kwitansi;
        });

        return redirect()->route('spp.kwitansi.show', $kwitansi)
            ->with('success', 'Kwitansi dibuat dan data SPP siswa sudah diperbarui.');
    }

    public function showKwitansi(Kwitansi $kwitansi)
    {
        $kwitansi->load(['siswa.kelas.jurusan', 'tahunAjaran', 'user', 'pembayaran.tagihan']);

        return view('halamanspp.show', ['kwitansi' => $kwitansi]);
    }
}
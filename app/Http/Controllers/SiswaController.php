<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    /** Pencarian seluruh siswa kelas 10, 11, 12 dari semua jurusan + status pembayaran. */
    public function index(Request $request)
    {
        $siswa = Siswa::with('kelas.jurusan')
        ->when($request->q, fn($q, $v) => $q->where(fn($w)=>$w->where('nama', 'like', "%$v%")->orWhere('nis', 'like', "%$v%")))
        ->when($request->tingkat, fn($q, $v) => $q->whereHas('kelas', fn($k) => $k->where('tingkat', $v)))
        ->when($request->jurusan_id, fn($q, $v) => $q->whereHas('kelas', fn($k) => $k->where('jurusan_id', $v)))
        ->when($request->kelas_id, fn($q, $v) => $q->where('kelas_id', $v))
        ->orderBy('nis')
        ->paginate(20)
        ->withQueryString();

        return view('siswa.index', [
            'siswa'   => $siswa,
            'jurusan' => Jurusan::orderBy('nama')->get(),
            'kelas'    => Kelas::when($request->jurusan_id, fn($q, $v)=>$q
            ->where('jurusan_id', $v))
            ->when($request->tingkat, fn($q, $v) => $q->where('tingkat', $v))
            ->orderBy('nama')->get(),
        ]);
    }

    public function show(Siswa $siswa)
    {
        $ta = TahunAjaran::aktif();
        $siswa->load('kelas.jurusan');
        $tagihan = $siswa->tagihan()
        ->whereHas('jenis', fn($q) => $q->where('nama', 'SPP'))
        ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
        ->get()
        ->keyBy('periode');
        
        return view('siswa.show', [
            'siswa'    => $siswa,
            'ta'       => $ta,
            'bulan'    => SppController::BULAN,
            'tagihan'  => $tagihan,
            'kwitansi' => Kwitansi::where('siswa_id', $siswa->nis)->latest('id')->get(),
        ]);
    }

    public function create()
    {
        return view('siswa.form', [
            'siswa' => new Siswa,
            'kelas' => Kelas::with('jurusan')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Siswa::create($this->validated($request));

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        return view('siswa.form', [
            'siswa' => $siswa,
            'kelas' => Kelas::with('jurusan')->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Siswa $siswa)
    {
        $siswa->update($this->validated($request, $siswa->nis));
        
        return redirect()->route('siswa.show', $siswa)->with('success', 'Data siswa berhasi diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', 'Siswa dihapus.');
    }

    private function validated(Request $request, $nis = null): array
    {
        return $request->validate([
            'nis'             => ['required', 'max:20', Rule::unique('siswa', 'nis')->ignore($nis, 'nis')],
            'nama'            => 'required|max:255',
            'jenis_kelamin'   => 'required|in:L,P',
            'kelas_id'        => 'required|exists:kelas,id',
            'tahun_masuk'     => 'required|digits:4',
            'alamat'          => 'nullable|max:225',
            'no_telepon'      => 'nullable|max:20',
            'email'           => 'nullable|email|max:255',
            'nama_wali'       => 'nullable|max:255',
            'no_telepon_wali' => 'nullable|max:20',
            'alamat_wali'     => 'nullable',
            'status'          => 'required|in:aktif,lulus,pindah',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    /** [model, aturan validasi, judul halaman] untuk tiap data master. */
    private function config(string $jenis): array
    {
        $map = [
            'jurusan' => [Jurusan::class, [
                'kode' => 'required|max:10|unique:jurusan,kode',
                'nama' => 'required|max:255',
                'spp'  => 'required|integer|min:0',
            ], 'Jurusan'],
            'kelas'   => [Kelas::class, [
                'jurusan_id' => 'required|exists:jurusan,id',
                'tingkat'    => 'required|in:10,11,12',
                'nama'       => 'required|max:255',
            ], 'Kelas'],
        ];

        return $map[$jenis] ?? abort(404);
    }

    public function index(string $jenis)
    {
        [$model, , $judul] = $this->config($jenis);

        $query = $jenis === 'kelas' ? Kelas::with('jurusan') : $model::query();

        return view('master.index', [
            'jenis'   => $jenis,
            'judul'   => $judul,
            'rows'    => $query->latest('id')->get(),
            'jurusan' => Jurusan::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request, string $jenis)
    {
        [$model, $rules] = $this->config($jenis);

        $model::create($request->validate($rules));

        return back()->with('success', 'Data tersimpan.');
    }

    public function destroy(string $jenis, int $id)
    {
        [$model] = $this->config($jenis);
        $model::findOrFail($id)->delete();

        return back()->with('success', 'Data dihapus.');
    }

    /** Dipakai JavaScript untuk dropdown jurusan -> kelas. */
    public function kelasByJurusan(Request $request)
    {
        return Kelas::when($request->jurusan_id, fn($q, $v) => $q->where('jurusan_id', $v))
            ->when($request->tingkat, fn($q, $v) => $q->where('tingkat', $v))
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }
}
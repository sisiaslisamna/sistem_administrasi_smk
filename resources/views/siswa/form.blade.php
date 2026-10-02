@extends('halamanutama.tampilanutama')
@section('title', $siswa->exists ? 'Edit siswa' : 'Tambah siswa')
@section('heading', $siswa->exists ? 'Edit siswa' : 'Tambah siswa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/siswa-form.css') }}">
@endpush

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ $siswa->exists ? route('siswa.update', $siswa) : route('siswa.store') }}">
        @csrf
        @if($siswa->exists) @method('PUT') @endif

        <div class="form-grid">
            <div class="form-row">
                <label for="nis">NIS</label>
                <input id="nis" name="nis" value="{{ old('nis', $siswa->nis) }}" required>
            </div>
            <div class="form-row">
                <label for="nama">Nama lengkap</label>
                <input id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}" required>
            </div>
            <div class="form-row">
                <label for="jk">Jenis kelamin</label>
                <select id="jk" name="jenis_kelamin">
                    <option value="L" @selected(old('jenis_kelamin', $siswa->jenis_kelamin) == 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $siswa->jenis_kelamin) == 'P')>Perempuan</option>
                </select>
            </div>
            <div class="form-row">
                <label for="kelas_id">Kelas</label>
                <select id="kelas_id" name="kelas_id" required>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" @selected(old('kelas_id', $siswa->kelas_id) == $k->id)>{{ $k->nama }} ({{ $k->jurusan->kode }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <label for="tahun_masuk">Tahun masuk</label>
                <input id="tahun_masuk" name="tahun_masuk" type="number" min="2000" max="{{ date('Y') + 1 }}"value="{{ old('tahun_masuk', $siswa->tahun_masuk) }}" required>
            </div>
            <div class="form-row">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2">{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>
            <div class="form-row">
                <label for="no_telepon">Nomor telepon siswa</label>
                <input id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $siswa->no_telepon) }}">
            </div>
            <div class="form-row">
                <label for="email">Email siswa</label>
                <input id="email" name="email" type="email" value="{{ old('email', $siswa->email) }}">
            </div>
            <div class="form-row">
                <label for="nama_wali">Nama wali</label>
                <input id="nama_wali" name="nama_wali" value="{{ old('nama_wali', $siswa->nama_wali) }}">
            </div>
            <div class="form-row">
                <label for="no_telepon_wali">Nomor telepon wali</label>
                <input id="no_telepon_wali" name="no_telepon_wali" value="{{ old('no_telepon_wali', $siswa->no_telepon_wali) }}">
            </div>
            <div class="form-row">
                <label for="alamat_wali">Alamat wali</label>
                <textarea id="alamat_wali" name="alamat_wali" rows="2">{{ old('alamat_wali', $siswa->alamat_wali) }}</textarea>
            </div>
            <div class="form-row">
                <label for="status">Status siswa</label>
                <select id="status" name="status">
                    @foreach(['aktif' => 'Aktif', 'lulus' => 'Lulus', 'pindah' => 'Pindah'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('status', $siswa->status ?? 'aktif') == $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn">Simpan siswa</button>
            <a class="btn btn-gray" href="{{ $siswa->exists ? route('siswa.show', $siswa) : route('siswa.index') }}">Batal</a>
        </div>
    </form>
</section>
@endsection

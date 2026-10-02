@extends('halamanutama.tampilanutama')
@section('title', $judul)
@section('heading', $judul)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/master.css') }}">
@endpush

@section('content')
<section class="card">
    <h2 class="section-title">Tambah {{ strtolower($judul) }}</h2>
    <form method="POST" action="{{ route('master.store', $jenis) }}" class="inline-form">
        @csrf
        @if($jenis === 'jurusan')
            <input name="kode" placeholder="Kode, contoh TKJ" value="{{ old('kode') }}" required>
            <input name="nama" placeholder="Nama jurusan" value="{{ old('nama') }}" required class="grow">
            <input type="number" name="spp" placeholder="SPP per bulan (Rp)" min="0" value="{{ old('spp') }}" required>
        @else
            <select name="jurusan_id" required>
                @foreach($jurusan as $j)<option value="{{ $j->id }}">{{ $j->kode }} - {{ $j->nama }}</option>@endforeach
            </select>
            <select name="tingkat" required>
                <option value="10">Kelas 10</option><option value="11">Kelas 11</option><option value="12">Kelas 12</option>
            </select>
            <input name="nama" placeholder="Nama rombel, contoh X TKJ 1" value="{{ old('nama') }}" required class="grow">
        @endif
        <button class="btn">Tambah</button>
    </form>
</section>

<section class="card">
    <div class="table-wrap">
        <table class="table-stack">
            <thead>
                <tr>
                    @if($jenis === 'jurusan')
                        <th>Kode</th><th>Nama jurusan</th><th class="num">SPP per bulan</th>
                    @else
                        <th>Rombel</th><th>Tingkat</th><th>Jurusan</th>
                    @endif
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $r)
                <tr>
                    @if($jenis === 'jurusan')
                        <td data-label="Kode">{{ $r->kode }}</td>
                        <td data-label="Nama jurusan">{{ $r->nama }}</td>
                        <td class="num" data-label="SPP per bulan">Rp {{ number_format($r->spp, 0, ',', '.') }}</td>
                    @else
                        <td data-label="Rombel">{{ $r->nama }}</td>
                        <td data-label="Tingkat">{{ $r->tingkat }}</td>
                        <td data-label="Jurusan">{{ $r->jurusan->kode }}</td>
                    @endif
                    <td class="act" data-label="">
                        <form method="POST" action="{{ route('master.destroy', [$jenis, $r->id]) }}" onsubmit="return confirm('Hapus data ini? Data yang terkait ikut terhapus.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-red btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">Belum ada data. Tambahkan lewat formulir di atas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
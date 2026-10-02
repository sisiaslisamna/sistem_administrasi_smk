@extends('halamanutama.tampilanutama')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<p class="dash-ta">Tahun ajaran aktif: <strong>{{ $ta->nama ?? 'belum ada pembayaran' }}</strong></p>
<section class="card dash-spp">
    <span class="dash-label">Total SPP terbayar (Januari – Desember, seluruh siswa)</span>
    <p class="dash-figure dash-ok">Rp {{ number_format($sppTerbayar, 0, ',', '.') }}</p>
    <p class="dash-note">
        {{ number_format($sppLunas) }} pembayaran SPP bulanan sudah lunas.
        <a href="{{ route('spp.index') }}">Lihat Data SPP</a>
    </p>
</section>

<section class="dash-cards">
    <div class="card">
        <span class="dash-label">Total SPP terbayar (Januari – Desember, seluruh siswa)</span>
        <p class="dash-figure dash-ok">Rp {{ number_format($sppTerbayar, 0, ',', '.') }}</p>
        <a class="btn btn-sm" href="{{ route('spp.index') }}">Lihat Data SPP</a>
    </div>

    <div class="card">
        <span class="dash-label">Siswa aktif</span>
        <p class="dash-figure">{{ number_format($totalSiswa) }}</p>
        <a class="btn btn-sm" href="{{ route('siswa.index') }}">Lihat data siswa</a>
    </div>

    <div class="card">
        <span class="dash-label">Pembayaran SPP bulanan lunas</span>
        <p class="dash-figure">{{ number_format($sppLunas) }}</p>
    </div>
</section>

<section class="card">
    <h2 class="section-title">Kwitansi SPP terbaru</h2>
    <div class="table-wrap">
        <table class="table-stack">
            <thead>
                <tr><th>Tanggal</th><th>No. kwitansi</th><th>Siswa</th><th>Metode</th><th class="num">Jumlah</th></tr>
            </thead>
            <tbody>
            @forelse($terbaru as $k)
                <tr>
                    <td data-label="Tanggal">{{ $k->tanggal->format('d/m/Y') }}</td>
                    <td data-label="No. kwitansi">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('spp.kwitansi.show', $k) }}">{{ $k->nomor }}</a>
                        @else
                            {{ $k->nomor }}
                        @endif
                    </td>
                    <td data-label="Siswa"><a href="{{ route('siswa.show', $k->siswa) }}">{{ $k->siswa->nama }}</a></td>
                    <td data-label="Metode">{{ ucfirst($k->metode) }}</td>
                    <td class="num" data-label="Jumlah">Rp {{ number_format($k->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada kwitansi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
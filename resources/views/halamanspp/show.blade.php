@extends('halamanutama.tampilanutama')
@section('title', 'Kwitansi ' . $kwitansi->nomor)
@section('heading', 'Kwitansi pembayaran SPP')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/spp.css') }}">
@endpush

@section('content')
<section class="card kwitansi">
    <header class="kw-head">
        <div>
            <h2>KWITANSI PEMBAYARAN SPP</h2>
            <p class="muted">Tahun ajaran {{ $kwitansi->tahunAjaran->nama }}</p>
        </div>
        <div class="kw-nomor">
            No. {{ $kwitansi->nomor }}<br>
            {{ $kwitansi->tanggal->format('d/m/Y') }}
        </div>
    </header>

    <dl class="profile-grid">
        <div><dt>NIS</dt><dd>{{ $kwitansi->siswa->nis }}</dd></div>
        <div><dt>Nama lengkap</dt><dd>{{ $kwitansi->siswa->nama }}</dd></div>
        <div><dt>Kelas</dt><dd>{{ $kwitansi->siswa->kelas->nama }}</dd></div>
        <div><dt>Jurusan</dt><dd>{{ $kwitansi->siswa->kelas->jurusan->nama }}</dd></div>
    </dl>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Rincian</th><th class="num">Jumlah</th></tr></thead>
            <tbody>
            @foreach($kwitansi->pembayaran as $p)
                <tr>
                    <td>SPP {{ $p->tagihan->periode }}</td>
                    <td class="num">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="kw-total">
                <td>Total</td>
                <td class="num">Rp {{ number_format($kwitansi->total, 0, ',', '.') }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <p class="muted">Metode: {{ ucfirst($kwitansi->metode) }} · Diterima oleh: {{ $kwitansi->user->name }}</p>
    @if($kwitansi->keterangan)<p class="muted">Keterangan: {{ $kwitansi->keterangan }}</p>@endif
</section>

<div class="kw-actions no-print">
    <button class="btn" onclick="window.print()">Cetak kwitansi</button>
    <a class="btn btn-gray" href="{{ route('spp.index') }}">Kembali ke Data SPP</a>
</div>
@endsection
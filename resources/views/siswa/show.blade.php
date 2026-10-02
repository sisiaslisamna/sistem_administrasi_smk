@extends('halamanutama.tampilanutama')
@section('title', $siswa->nama)
@section('heading', 'Detail siswa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/siswa-show.css') }}">
@endpush

@section('content')
<section class="card profile">
    <div class="profile-head">
        <div>
            <h2>{{ $siswa->nama }}</h2>
            <p class="muted">NIS {{ $siswa->nis }}</p>
        </div>
        @if(auth()->user()->isAdmin())
            <div class="profile-actions">
                <a class="btn btn-sm" href="{{ route('siswa.edit', $siswa) }}">Edit siswa</a>
                <form method="POST" action="{{ route('siswa.destroy', $siswa) }}" onsubmit="return confirm('Hapus siswa ini beserta seluruh tagihannya?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-red btn-sm">Hapus</button>
                </form>
            </div>
        @endif
    </div>

    <dl class="profile-grid">
        <div><dt>Kelas</dt><dd>{{ $siswa->kelas->nama }}</dd></div>
        <div><dt>Jurusan</dt><dd>{{ $siswa->kelas->jurusan->nama }}</dd></div>
        <div><dt>Jenis kelamin</dt><dd>{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
        <div><dt>Tahun masuk</dt><dd>{{ $siswa->tahun_masuk }}</dd></div>
        <div><dt>Alamat</dt><dd>{{ $siswa->alamat ?? '-' }}</dd></div>
        <div><dt>No. telepon</dt><dd>{{ $siswa->no_telepon ?? '-' }}</dd></div>
        <div><dt>Email</dt><dd>{{ $siswa->email ?? '-' }}</dd></div>
        <div><dt>Nama wali</dt><dd>{{ $siswa->nama_wali ?? '-' }}</dd></div>
        <div><dt>No. telepon wali</dt><dd>{{ $siswa->no_telepon_wali ?? '-' }}</dd></div>
        <div><dt>Alamat wali</dt><dd>{{ $siswa->alamat_wali ?? '-' }}</dd></div>
        <div><dt>Status</dt><dd>{{ ucfirst($siswa->status) }}</dd></div>
    </dl>
</section>

<section class="card">
    <div class="profile-head">
        <h2 class="section-title">SPP tahun ajaran {{ $ta->nama ?? '-' }}</h2>
        @if(auth()->user()->isAdmin())
            <a class="btn btn-sm" href="{{ route('spp.kwitansi.create', ['nis' => $siswa->nis]) }}">Bayar SPP</a>
        @endif
    </div>
    <p class="muted">SPP per bulan: Rp {{ number_format($siswa->kelas->jurusan->spp, 0, ',', '.') }}</p>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>@foreach($bulan as $b)<th class="num">{{ mb_substr($b, 0, 3) }}</th>@endforeach</tr>
            </thead>
            <tbody>
                <tr>
                @foreach($bulan as $b)
                    @php $st = $tagihan->get($b)?->status ?? 'belum'; @endphp
                    <td class="num"><span class="badge b-{{ $st }}">{{ $st === 'lunas' ? 'Lunas' : ($st === 'cicil' ? 'Cicil' : 'Belum') }}</span></td>
                @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2 class="section-title">Riwayat kwitansi</h2>
    <div class="table-wrap">
        <table class="table-stack">
            <thead><tr><th>Tanggal</th><th>No. kwitansi</th><th>Metode</th><th class="num">Jumlah</th></tr></thead>
            <tbody>
            @forelse($kwitansi as $k)
                <tr>
                    <td data-label="Tanggal">{{ $k->tanggal->format('d/m/Y') }}</td>
                    <td data-label="No. kwitansi">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('spp.kwitansi.show', $k) }}">{{ $k->nomor }}</a>
                        @else
                            {{ $k->nomor }}
                        @endif
                    </td>
                    <td data-label="Metode">{{ ucfirst($k->metode) }}</td>
                    <td class="num" data-label="Jumlah">Rp {{ number_format($k->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">Belum ada kwitansi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

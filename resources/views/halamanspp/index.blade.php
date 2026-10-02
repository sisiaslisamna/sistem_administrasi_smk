@extends('halamanutama.tampilanutama')
@section('title', 'Data SPP')
@section('heading', 'Data SPP')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/spp.css') }}">
@endpush

@section('content')
<div class="card">
    <form method="GET" class="spp-filters">
        <input type="search" name="q" placeholder="Cari nama atau NIS" value="{{ request('q') }}">
        <select name="tingkat">
            <option value="">Semua tingkat</option>
            @foreach([10, 11, 12] as $t)
                <option value="{{ $t }}" @selected(request('tingkat') == $t)>Kelas {{ $t }}</option>
            @endforeach
        </select>
        <select name="jurusan_id">
            <option value="">Semua jurusan</option>
            @foreach($jurusan as $j)
                <option value="{{ $j->id }}" @selected(request('jurusan_id') == $j->id)>{{ $j->kode }}</option>
            @endforeach
        </select>
        <button class="btn">Terapkan</button>
    </form>
</div>

<div class="card">
    <p class="result-count">{{ number_format($siswa->total()) }} siswa aktif · Tahun ajaran {{ $ta->nama ?? '-' }}</p>

    <div class="table-wrap">
        <table class="spp-table">
            <thead>
                <tr>
                    <th>NIS</th><th>Nama Lengkap</th><th>Kelas</th><th class="num">SPP/bulan</th>
                    @foreach($bulan as $b)<th class="cell">{{ mb_substr($b, 0, 3) }}</th>@endforeach
                    @if(auth()->user()->isAdmin())<th></th>@endif
                </tr>
            </thead>
            <tbody>
            @forelse($siswa as $s)
                @php $per = $s->tagihan->keyBy('periode'); @endphp
                <tr>
                    <td>{{ $s->nis }}</td>
                    <td class="name">{{ $s->nama }}</td>
                    <td>{{ $s->kelas->nama }}</td>
                    <td class="num">Rp {{ number_format($s->kelas->jurusan->spp, 0, ',', '.') }}</td>
                    @foreach($bulan as $b)
                        @php $st = $per->get($b)?->status ?? 'belum'; @endphp
                        <td class="cell">
                            <span class="badge b-{{ $st }}" title="{{ $b }}">
                                {{ $st === 'lunas' ? 'Lunas' : ($st === 'cicil' ? 'Cicil' : 'Belum') }}
                            </span>
                        </td>
                    @endforeach
                    @if(auth()->user()->isAdmin())
                        <td><a class="btn btn-sm" href="{{ route('spp.kwitansi.create', ['nis' => $s->nis]) }}">Bayar</a></td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="17" class="empty">Tidak ada siswa ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $siswa->links('pagination') }}
</div>
@endsection
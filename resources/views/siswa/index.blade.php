@extends('halamanutama.tampilanutama')
@section('heading', 'Data siswa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/siswa-index.css') }}">
@endpush

@section('content')
<div class="card">
    <form method="GET" class="filters" id="filter-form">
        <input type="search" name="q" id="q" placeholder="Cari nama atau NIS" value="{{ request('q') }}">

        <select name="tingkat" id="tingkat">
            <option value="">Semua tingkat</option>
            @foreach([10, 11, 12] as $t)
                <option value="{{ $t }}" @selected(request('tingkat') == $t)>Kelas {{ $t }}</option>
            @endforeach
        </select>

        <select name="jurusan_id" id="jurusan">
            <option value="">Semua jurusan</option>
            @foreach($jurusan as $j)
                <option value="{{ $j->id }}" @selected(request('jurusan_id') == $j->id)>{{ $j->kode }}</option>
            @endforeach
        </select>

        <select name="kelas_id" id="kelas">
            <option value="">Semua rombel</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>

        <button class="btn">Cari</button>
    </form>
</div>

<div class="card">
    <p class="result-count">{{ number_format($siswa->total()) }} siswa ditemukan</p>

    <div class="table-wrap">
        <table class="table-stack">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama lengkap</th>
                    <th>Jenis kelamin</th>
                    <th>Kelas</th>
                    <th>No. Telepon</th>
                    <th>Alamat</th>
                    <th>Tahun masuk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa as $s)
                <tr>
                    <td data-label="NIS">{{ $s->nis }}</td>
                    <td class="name" data-label="Nama lengkap">{{ $s->nama }}</td>
                    <td data-label="Jenis kelamin">{{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td data-label="Kelas">{{ $s->kelas->nama }}</td>
                    <td data-label="No. telepon">{{ $s->no_telepon ?? '-' }}</td>
                    <td data-label="Alamat">{{ $s->alamat }}</td>
                    <td data-label="Tahun masuk">{{ $s->tahun_masuk }}</td>
                    <td data-label="Status">{{ ucfirst($s->status) }}</td>
                    <td data-label=""><a class="btn btn-sm" href="{{ route('siswa.show', $s) }}">Detail</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty">Tidak ada siswa yang cocok. Ubah kata kunci atau filter.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $siswa->links('pagination') }}
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('filter-form');

    // Cari otomatis setelah berhenti mengetik
    let timer;
    document.getElementById('q').addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 500);
    });

    ['tingkat', 'kelas'].forEach(id =>
        document.getElementById(id).addEventListener('change', () => form.submit()));

    // Pilih jurusan: daftar rombel menyesuaikan
    document.getElementById('jurusan').addEventListener('change', async function () {
        const tingkat = document.getElementById('tingkat').value;
        const res = await fetch(`{{ route('api.kelas') }}?jurusan_id=${this.value}&tingkat=${tingkat}`);
        const data = await res.json();
        document.getElementById('kelas').innerHTML =
            '<option value="">Semua rombel</option>' +
            data.map(k => `<option value="${k.id}">${k.nama}</option>`).join('');
        form.submit();
    });
</script>
@endpush

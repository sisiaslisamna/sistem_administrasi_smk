@extends('halamanutama.tampilanutama')
@section('title', 'Buat kwitansi SPP')
@section('heading', 'Buat kwitansi pembayaran SPP')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/spp.css') }}">
@endpush

@section('content')
<section class="card">
    <h2 class="section-title">Pilih siswa</h2>
    <form method="GET" action="{{ route('spp.kwitansi.create') }}" class="form-row">
        <label for="nis">Siswa</label>
        <select id="nis" name="nis" onchange="this.form.submit()">
            <option value="">-- Pilih siswa --</option>
            @foreach($daftarSiswa as $d)
                <option value="{{ $d->nis }}" @selected(($siswa->nis ?? null) === $d->nis)>{{ $d->nis }} - {{ $d->nama }}</option>
            @endforeach
        </select>
    </form>
</section>

@if($siswa)
<form method="POST" action="{{ route('spp.kwitansi.store') }}" class="card">
    @csrf
    <input type="hidden" name="siswa_id" value="{{ $siswa->nis }}">

    <h2 class="section-title">Pembayaran SPP — {{ $siswa->nama }}</h2>
    <p class="muted">
        {{ $siswa->kelas->nama }} · {{ $siswa->kelas->jurusan->nama }} ·
        SPP Rp <span id="nominal" data-nominal="{{ $siswa->kelas->jurusan->spp }}">{{ number_format($siswa->kelas->jurusan->spp, 0, ',', '.') }}</span>/bulan ·
        Tahun ajaran {{ $ta->nama ?? '-' }}
    </p>

    <div class="bulan-grid">
        @foreach($bulan as $b)
            @php $isLunas = in_array($b, $bulanLunas); @endphp
            <label class="bulan-item {{ $isLunas ? 'is-lunas' : '' }}">
                <input type="checkbox" name="bulan[]" value="{{ $b }}"
                       @disabled($isLunas) @checked(in_array($b, old('bulan', [])))>
                {{ $b }}
                @if($isLunas)<span class="badge b-lunas">Lunas</span>@endif
            </label>
        @endforeach
    </div>

    <div class="form-row">
        <label for="tanggal">Tanggal bayar</label>
        <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required>
    </div>
    <div class="form-row">
        <label for="metode">Metode</label>
        <select id="metode" name="metode">
            <option value="tunai">Tunai</option>
            <option value="transfer">Transfer</option>
        </select>
    </div>
    <div class="form-row">
        <label for="keterangan">Keterangan (opsional)</label>
        <input id="keterangan" name="keterangan" value="{{ old('keterangan') }}" maxlength="255">
    </div>

    <p class="kw-preview">Total: <strong id="total">Rp 0</strong></p>
    <button class="btn">Simpan dan buat kwitansi</button>
</form>
@endif
@endsection

@push('scripts')
<script>
    const nominalEl = document.getElementById('nominal');
    if (nominalEl) {
        const nominal = parseInt(nominalEl.dataset.nominal);
        const boxes = document.querySelectorAll('input[name="bulan[]"]');
        const total = document.getElementById('total');
        const hitung = () => {
            const n = [...boxes].filter(b => b.checked).length * nominal;
            total.textContent = 'Rp ' + n.toLocaleString('id-ID');
        };
        boxes.forEach(b => b.addEventListener('change', hitung));
        hitung();
    }
</script>
@endpush
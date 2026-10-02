<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Administrasi SMK')</title>
    <link rel="stylesheet" href="{{ asset('css/tampilanutama.css') }}">
    @stack('styles')
</head>
<body class="@yield('body-class')">

@auth
<button class="menu-toggle" id="menuToggle" type="button" aria-label="Buka menu"
aria-expanded="false" aria-controls="sidebar">&#9776;</button>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <strong>SMK</strong>
        <span>Science Technology & Business</span>
    </div>

    <nav>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('siswa.index') }}" class="{{ request()->routeIs('siswa.*') ? 'active' : '' }}">Data Siswa</a>
        <a href="{{ route('spp.index') }}" class="{{ request()->routeIs('spp.index') ? 'active' : '' }}">Data SPP Siswa</a>
        
        @if(auth()->user()->isAdmin())
        <p class="nav-group">Administrasi</p>
        <a href="{{ route('spp.kwitansi.create') }}" class="{{ request()->routeIs('spp.kwitansi.*') ? 'active' : '' }}">Buat kwitansi SPP</a>
        <a href="{{ route('siswa.create') }}" class="{{ request()->routeIs('siswa.create') ? 'active' : '' }}">Tambah siswa</a>
        <a href="{{ route('master.index', 'jurusan') }}" class="{{ request()->is('master/jurusan') ? 'active' : '' }}">Jurusan</a>
        <a href="{{ route('master.index', 'kelas') }}" class="{{ request()->is('master/kelas') ? 'active' : '' }}">Kelas</a>
        @endif
    </nav>
</aside>
@endauth

<main class="content">
    @auth
    <header class="topbar">
        <h1>@yield('heading')</h1>
        <form method="POST" action="{{ route('logout') }}" class="topbar-user">
            @csrf
            <span>{{ auth()->user()->name }}
                <small>{{ auth()->user()->isAdmin() ? 'Administrator' : 'Kepala sekolah' }}</small></span>
            <button class="btn btn-gray btn-sm">Keluar</button>
        </form>
    </header>
    @endauth

    @if(session('success')) <div class="alert alert-ok">{{ session('success') }}</div> @endif
    @if($errors->any())     <div class="alert alert-err">{{ $errors->first() }}</div> @endif

    @yield('content')
</main>

@auth
<script>
    (function () {
        var body = document.body, toggle = document.getElementById('menuToggle');
        if (!toggle) return;
        function setMenu(open) {
            body.classList.toggle('menu-open', open);
            toggle.setAttribute('aria-expanded', open);
        }
        toggle.addEventListener('click', function () { setMenu(!body.classList.contains('menu-open')); });
        document.getElementById('sidebarBackdrop').addEventListener('click', function () { setMenu(false); });
        document.querySelectorAll('#sidebar nav a').forEach(function (a) {
            a.addEventListener('click', function () { setMenu(false); });
        });
    })();
</script>
@endauth
@stack('scripts')
</body>
</html>

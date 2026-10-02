@extends('halamanutama.tampilanutama')
@section('title', 'Administrasi Login')
@section('body-class', 'page-login')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<section class="login-card">
    <img class="login-logo" src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">

    <h1 class="login-title">SMK <br> Science Technology & Business</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="login-field">
            <label for="username">Nama Pengguna</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}"
                   placeholder="Masukkan Nama Pengguna" required autofocus autocomplete="username">
        </div>

        <div class="login-field">
            <label for="password">Kata Sandi</label>
            <input id="password" type="password" name="password"
                   placeholder="Masukkan Kata Sandi" required autocomplete="current-password">
        </div>

        @if($errors->any())
            <p class="login-error">{{ $errors->first() }}</p>
        @endif

        <button class="login-button">Masuk</button>
    </form>

    <p class="login-footer">&copy;Hak Cipta SMK Science Technology & Business</p>
</section>
@endsection
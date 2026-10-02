<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SppController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {

    // ===== Khusus Administrator (di atas agar /siswa/create tidak bentrok dengan /siswa/{siswa}) =====
    Route::middleware('role:admin')->group(function () {
        Route::resource('siswa', SiswaController::class)->except(['index', 'show']);

        // Jurusan dan kelas
        Route::get('/master/{jenis}', [MasterController::class, 'index'])->name('master.index');
        Route::post('/master/{jenis}', [MasterController::class, 'store'])->name('master.store');
        Route::delete('/master/{jenis}/{id}', [MasterController::class, 'destroy'])->name('master.destroy');

        // Kwitansi SPP
        Route::get('/spp/kwitansi/create', [SppController::class, 'createKwitansi'])->name('spp.kwitansi.create');
        Route::post('/spp/kwitansi', [SppController::class, 'storeKwitansi'])->name('spp.kwitansi.store');
        Route::get('/spp/kwitansi/{kwitansi}', [SppController::class, 'showKwitansi'])->name('spp.kwitansi.show');
    });

    // ===== Administrator + Kepala Sekolah =====
    Route::middleware('role:admin,kepala_sekolah')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
        Route::get('/siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');
        Route::get('/spp', [SppController::class, 'index'])->name('spp.index');
        Route::get('/api/kelas', [MasterController::class, 'kelasByJurusan'])->name('api.kelas');
    });
});
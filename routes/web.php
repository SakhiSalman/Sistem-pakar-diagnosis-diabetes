<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GejalaController;
use App\Http\Controllers\Admin\KategoriTesLabController;
use App\Http\Controllers\Admin\RuleCfController;
use App\Http\Controllers\Admin\RuleCfLabController;
use App\Http\Controllers\Kuesioner\KuesionerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KuesionerController::class, 'index'])->name('home');

// ===== Kuesioner Publik (Pasien) =====
Route::prefix('kuesioner')->name('kuesioner.')->group(function () {
    Route::get('/', [KuesionerController::class, 'index'])->name('index');
    Route::post('/login-lama', [KuesionerController::class, 'loginLama'])->name('login-lama');
    Route::post('/daftar', [KuesionerController::class, 'daftar'])->name('daftar');

    Route::get('/gejala', [KuesionerController::class, 'gejala'])->name('gejala');
    Route::post('/proses', [KuesionerController::class, 'proses'])->name('proses');

    Route::get('/tes-lab', [KuesionerController::class, 'formTesLab'])->name('tes-lab');
    Route::post('/tes-lab', [KuesionerController::class, 'simpanTesLab'])->name('tes-lab.simpan');

    Route::get('/hasil', [KuesionerController::class, 'hasil'])->name('hasil');
    Route::get('/jadwal', [KuesionerController::class, 'formJadwal'])->name('jadwal');
    Route::post('/simpan-jadwal', [KuesionerController::class, 'simpanJadwal'])->name('simpan-jadwal');

    // Lupa password pasien
    Route::get('/lupa-password', [KuesionerController::class, 'formLupaPassword'])->name('lupa-password');
    Route::post('/lupa-password', [KuesionerController::class, 'kirimLinkReset'])->name('lupa-password.kirim');
    Route::get('/reset-password/{token}', [KuesionerController::class, 'formResetPassword'])->name('reset-password.form');
    Route::post('/reset-password', [KuesionerController::class, 'prosesResetPassword'])->name('reset-password.simpan');
});

// ===== Login Admin/Staf =====
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'proses'])->name('login.post');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'prosesRegister'])->name('register.post');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Lupa password admin/staf
    Route::get('/lupa-password', [AuthController::class, 'formLupaPassword'])->name('lupa-password');
    Route::post('/lupa-password', [AuthController::class, 'kirimLinkReset'])->name('lupa-password.kirim');
    Route::get('/reset-password/{token}', [AuthController::class, 'formResetPassword'])->name('reset-password.form');
    Route::post('/reset-password', [AuthController::class, 'prosesResetPassword'])->name('reset-password.simpan');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/detail/{id}', [DashboardController::class, 'detail'])->name('dashboard.detail');
        Route::post('/dashboard/validasi/{id}', [DashboardController::class, 'validasi'])->name('dashboard.validasi');

        Route::prefix('gejala')->name('gejala.')->group(function () {
            Route::get('/', [GejalaController::class, 'index'])->name('index');
            Route::get('/tambah', [GejalaController::class, 'tambah'])->name('tambah');
            Route::post('/simpan', [GejalaController::class, 'simpan'])->name('simpan');
            Route::get('/edit/{id}', [GejalaController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [GejalaController::class, 'update'])->name('update');
            Route::get('/hapus/{id}', [GejalaController::class, 'hapus'])->name('hapus');
        });

        Route::prefix('rule-cf')->name('rule-cf.')->group(function () {
            Route::get('/', [RuleCfController::class, 'index'])->name('index');
            Route::post('/simpan', [RuleCfController::class, 'simpan'])->name('simpan');
        });

        Route::prefix('rule-cf-lab')->name('rule-cf-lab.')->group(function () {
            Route::get('/', [RuleCfLabController::class, 'index'])->name('index');
            Route::post('/simpan', [RuleCfLabController::class, 'simpan'])->name('simpan');
        });

        Route::prefix('kategori-lab')->name('kategori-lab.')->group(function () {
            Route::get('/', [KategoriTesLabController::class, 'index'])->name('index');
            Route::get('/tambah', [KategoriTesLabController::class, 'tambah'])->name('tambah');
            Route::post('/simpan', [KategoriTesLabController::class, 'simpan'])->name('simpan');
            Route::get('/edit/{id}', [KategoriTesLabController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [KategoriTesLabController::class, 'update'])->name('update');
            Route::post('/toggle/{id}', [KategoriTesLabController::class, 'toggleAktif'])->name('toggle');
            Route::get('/hapus/{id}', [KategoriTesLabController::class, 'hapus'])->name('hapus');
        });
    });
});

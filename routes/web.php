<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Livewire\Admin\KelasCrud;
use App\Http\Livewire\Admin\PenggunaCrud;
use App\Http\Livewire\Admin\SiswaCrud;
use App\Http\Livewire\Admin\WaliKelasCrud;
use App\Http\Livewire\Admin\WaliMuridCrud;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::middleware(['auth'])->group(function () {

    // Rute Pengelolaan Data Dasar (HANYA UNTUK ADMIN)
    Route::middleware(['role:admin'])->group(function () {

        Route::view('/users', 'admin.users')->name('users');             // pengelolaan akun pengguna
        Route::view('/siswa', 'admin.siswa')->name('siswa');             // pengelolaan siswa
        Route::view('/wali-murid', 'admin.walimurid')->name('wali-murid'); // pengelolaan wali murid
        Route::view('/wali-kelas', 'admin.walikelas')->name('wali-kelas'); // pengelolaan wali kelas
        Route::view('/kelas', 'admin.kelas')->name('kelas');
    });

    // routes/web.php
    Route::middleware(['auth', 'guru_bk'])->group(function () {
        Route::get('/jenis-pelanggaran', function () {
            return view('jenispelanggaran.index');
        })->name('jenis-pelanggaran');
    });

    // Rute CRUD Pelanggaran (UNTUK ADMIN DAN GURU BK, atau role lain yang Anda izinkan)
    // Perbaikan ada di sini: role:admin,guru_bk
    // Untuk orang_tua: hanya boleh lihat
    Route::middleware(['role:guru_bk,wali_kelas,orang_tua'])->group(function () {
        Route::get('/pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran.index');
        Route::get('/monitoring', function () {
            return view('monitoring.index');
        })->name('monitoring.index');
    });

    // Untuk guru_bk dan wali_kelas: boleh CRUD
    Route::middleware(['role:guru_bk,wali_kelas'])->group(function () {
        Route::get('/pelanggaran/create', [PelanggaranController::class, 'create'])->name('pelanggaran.create');
        Route::post('/pelanggaran', [PelanggaranController::class, 'store'])->name('pelanggaran.store');
        Route::get('/pelanggaran/{pelanggaran}/edit', [PelanggaranController::class, 'edit'])->name('pelanggaran.edit');
        Route::put('/pelanggaran/{pelanggaran}', [PelanggaranController::class, 'update'])->name('pelanggaran.update');
        Route::delete('/pelanggaran/{pelanggaran}', [PelanggaranController::class, 'destroy'])->name('pelanggaran.destroy');
    });

    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/',              [NotifikasiController::class, 'index'])->name('index');
        Route::post('/{notifikasi}/read', [NotifikasiController::class, 'markAsRead'])->name('read');
        Route::post('/read-all',     [NotifikasiController::class, 'markAllRead'])->name('read-all');
    });
});

require __DIR__ . '/auth.php';

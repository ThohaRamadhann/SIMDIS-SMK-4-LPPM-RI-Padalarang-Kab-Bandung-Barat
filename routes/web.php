<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelanggaranController;
use App\Http\Livewire\Admin\PenggunaCrud;
use App\Http\Livewire\Admin\KelasCrud;
use App\Http\Livewire\Admin\SiswaCrud;
use App\Http\Livewire\Admin\WaliKelasCrud;
use App\Http\Livewire\Admin\WaliMuridCrud;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');



Route::middleware(['auth'])->group(function () {

    // Admin-only area
    Route::middleware(['role:admin'])->group(function () {
    
    Route::view('/users', 'admin.users')->name('users');                 // pengelolaan akun pengguna
    Route::view('/siswa', 'admin.siswa')->name('siswa');                 // pengelolaan siswa
    Route::view('/wali-murid', 'admin.walimurid')->name('wali-murid');  // pengelolaan wali murid
    Route::view('/wali-kelas', 'admin.walikelas')->name('wali-kelas');  // pengelolaan wali kelas
    Route::view('/kelas', 'admin.kelas')->name('kelas');   

        // CRUD Pelanggaran
    Route::get('/pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran.index');
    Route::get('/pelanggaran/create', [PelanggaranController::class, 'create'])->name('pelanggaran.create');
    Route::post('/pelanggaran', [PelanggaranController::class, 'store'])->name('pelanggaran.store');
    Route::get('/pelanggaran/{pelanggaran}/edit', [PelanggaranController::class, 'edit'])->name('pelanggaran.edit');
    Route::put('/pelanggaran/{pelanggaran}', [PelanggaranController::class, 'update'])->name('pelanggaran.update');
    Route::delete('/pelanggaran/{pelanggaran}', [PelanggaranController::class, 'destroy'])->name('pelanggaran.destroy');

    });
});

require __DIR__.'/auth.php';

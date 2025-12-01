<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelanggaranController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    // CRUD Pelanggaran
    Route::get('/pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran.index');
    Route::get('/pelanggaran/create', [PelanggaranController::class, 'create'])->name('pelanggaran.create');
    Route::post('/pelanggaran', [PelanggaranController::class, 'store'])->name('pelanggaran.store');
    Route::get('/pelanggaran/{pelanggaran}/edit', [PelanggaranController::class, 'edit'])->name('pelanggaran.edit');
    Route::put('/pelanggaran/{pelanggaran}', [PelanggaranController::class, 'update'])->name('pelanggaran.update');
    Route::delete('/pelanggaran/{pelanggaran}', [PelanggaranController::class, 'destroy'])->name('pelanggaran.destroy');
});

require __DIR__.'/auth.php';

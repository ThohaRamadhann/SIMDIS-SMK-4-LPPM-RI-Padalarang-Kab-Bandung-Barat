<?php

use App\Http\Controllers\Admin\TemplateImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportPelanggaranController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\SuratPanggilanController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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

    // ── ADMIN ONLY ──────────────────────────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {

        Route::view('/users',      'admin.users')->name('users');
        Route::view('/siswa',      'admin.siswa')->name('siswa');
        Route::view('/wali-siswa', 'admin.walisiswa')->name('wali-siswa'); // sebelumnya: wali-murid
        Route::view('/wali-kelas', 'admin.walikelas')->name('wali-kelas');
        Route::view('/kelas',      'admin.kelas')->name('kelas');

        // Download template import
        Route::get('/admin/template/{type}', [TemplateImportController::class, 'download'])
            ->name('admin.template.download')
            ->where('type', 'pengguna|wali_kelas|wali_siswa|kelas|siswa'); // wali_murid → wali_siswa
    });

    // ── GURU BK ONLY ────────────────────────────────────────────────────────
    Route::middleware(['role:guru_bk'])->group(function () {

        Route::get('/jenis-pelanggaran', function () {
            return view('jenispelanggaran.index');
        })->name('jenis-pelanggaran');

        Route::get('/pelanggaran/create',           [PelanggaranController::class, 'create'])->name('pelanggaran.create');
        Route::post('/pelanggaran',                 [PelanggaranController::class, 'store'])->name('pelanggaran.store');
        Route::get('/pelanggaran/{pelanggaran}/edit', [PelanggaranController::class, 'edit'])->name('pelanggaran.edit');
        Route::put('/pelanggaran/{pelanggaran}',    [PelanggaranController::class, 'update'])->name('pelanggaran.update');
        Route::delete('/pelanggaran/{pelanggaran}', [PelanggaranController::class, 'destroy'])->name('pelanggaran.destroy');

        Volt::route('/pelanggaran/{id}/surat-panggilan', 'surat-panggilan.create')
            ->name('surat-panggilan.create');

        Route::get('/surat-panggilan/{id}/cetak', [SuratPanggilanController::class, 'cetak'])
            ->name('surat-panggilan.cetak');

        Route::get('/pelanggaran/export-pdf', [ExportPelanggaranController::class, 'export'])
            ->name('pelanggaran.export');
    });

    // ── GURU BK + WALI KELAS + WALI SISWA ──────────────────────────────────
    // wali_siswa menggantikan orang_tua — tetap bisa lihat pelanggaran & monitoring
    Route::middleware(['role:guru_bk,wali_kelas,wali_siswa'])->group(function () {
        Route::get('/pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran.index');
        Route::get('/monitoring',  function () {
            return view('monitoring.index');
        })->name('monitoring.index');
    });

    // ── NOTIFIKASI (semua role login) ───────────────────────────────────────
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/',                   [NotifikasiController::class, 'index'])->name('index');
        Route::post('/{notifikasi}/read', [NotifikasiController::class, 'markAsRead'])->name('read');
        Route::post('/read-all',          [NotifikasiController::class, 'markAllRead'])->name('read-all');
    });

    // ── LOG AKTIVITAS (semua role login) ────────────────────────────────────
    Volt::route('/log-aktivitas', 'log.log-aktivitas')
        ->middleware(['auth'])
        ->name('log.aktivitas');
});

require __DIR__ . '/auth.php';
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\PerizinanController;
use App\Http\Controllers\Admin\KalenderController;
use App\Http\Controllers\Guru\GuruDashboardController;
use App\Http\Controllers\Guru\SiswaController as GuruSiswaController;
use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;
use App\Http\Controllers\Guru\PerizinanController as GuruPerizinanController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\AttendanceReportController;


Route::get('/rekap-absensi', [AttendanceReportController::class, 'index'])
     ->name('attendance.report');

Route::get('/logout-confirm', [LogoutController::class, 'show'])->name('logout.confirm');
Route::post('/logout-confirm', [LogoutController::class, 'process'])->name('logout.process');

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ===== ADMIN ROUTES =====
Route::middleware(['auth.check', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // AJAX
    Route::put('/izin/update',  [AdminDashboardController::class, 'updateIzinStatus'])->name('izin.update');
    Route::get('/izin/refresh', [AdminDashboardController::class, 'refreshIzin'])->name('izin.refresh');

    // Guru
    Route::get('/guru',      [GuruController::class, 'index'])->name('guru.index');
    Route::post('/guru',     [GuruController::class, 'store'])->name('guru.store');
    Route::put('/guru',      [GuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru',   [GuruController::class, 'destroy'])->name('guru.destroy');
    Route::get('/guru/akun', [GuruController::class, 'akun'])->name('guru.akun');

    // Siswa
    Route::get('/siswa',      [SiswaController::class, 'index'])->name('siswa.index');
    Route::post('/siswa',     [SiswaController::class, 'store'])->name('siswa.store');
    Route::put('/siswa',      [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa',   [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/siswa/akun', [SiswaController::class, 'akun'])->name('siswa.akun');

    // Absensi
    Route::get('/absensi',         [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/simpan', [AbsensiController::class, 'simpan'])->name('absensi.simpan');
    Route::post('/absensi/export', [AbsensiController::class, 'exportData'])->name('absensi.export');

    // Perizinan
    Route::get('/perizinan',        [PerizinanController::class, 'index'])->name('perizinan.index');
    Route::put('/perizinan/update', [PerizinanController::class, 'updateStatus'])->name('perizinan.update');

    // Kalender
    Route::get('/kalender',        [KalenderController::class, 'index'])->name('kalender.index');
    Route::post('/kalender',       [KalenderController::class, 'store'])->name('kalender.store');
    Route::put('/kalender',        [KalenderController::class, 'update'])->name('kalender.update');
    Route::delete('/kalender',     [KalenderController::class, 'destroy'])->name('kalender.destroy');
    Route::get('/kalender/detail', [KalenderController::class, 'show'])->name('kalender.show');
});

// ===== GURU ROUTES =====
Route::middleware(['auth.check', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

    // Data Siswa (read-only)
    Route::get('/siswa', [GuruSiswaController::class, 'index'])->name('siswa.index');

    // AJAX
    Route::put('/izin/update',  [GuruDashboardController::class, 'updateIzinStatus'])->name('izin.update');
    Route::get('/izin/refresh', [GuruDashboardController::class, 'refreshIzin'])->name('izin.refresh');

    // Absensi
    Route::get('/absensi',         [GuruAbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/simpan', [GuruAbsensiController::class, 'simpan'])->name('absensi.simpan');
    Route::post('/absensi/export', [GuruAbsensiController::class, 'exportData'])->name('absensi.export');

    // Perizinan
    Route::get('/izin',                [GuruPerizinanController::class, 'index'])->name('izin.index');
    Route::put('/izin/update-status',  [GuruPerizinanController::class, 'updateStatus'])->name('izin.update.status');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\PerizinanController;
use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\FcmController;

/*
|--------------------------------------------------------------------------
| API Routes untuk Flutter OrtuConnect
|--------------------------------------------------------------------------
*/

// ── Public routes (tidak perlu token) ──────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ── Protected routes (wajib token) ─────────────────────────────────────
Route::middleware('api.auth')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profil
    Route::get('/profile',       [ProfileController::class, 'show']);
    Route::post('/profile',      [ProfileController::class, 'update']);
    Route::post('/upload-photo', [ProfileController::class, 'uploadPhoto']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index']);

    // Perizinan
    Route::get('/perizinan',        [PerizinanController::class, 'index']);
    Route::post('/perizinan',       [PerizinanController::class, 'store']);
    Route::get('/perizinan/status', [PerizinanController::class, 'cekStatus']);

    // Agenda / Kalender
    Route::get('/agenda',           [AgendaController::class, 'index']);
    Route::get('/agenda/mendatang', [AgendaController::class, 'mendatang']);

    // FCM Token
    Route::post('/fcm-token', [FcmController::class, 'store']);
});
